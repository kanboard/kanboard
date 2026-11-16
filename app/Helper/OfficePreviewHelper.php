<?php

namespace Kanboard\Helper;

use DOMDocument;
use DOMXPath;
use Kanboard\Core\Base;
use RuntimeException;
use ZipArchive;

class OfficePreviewHelper extends Base
{
	private const MAX_XML_SIZE = 16777216;
	private const MAX_ROWS = 5000;
	private const MAX_COLUMNS = 100;

	public function render(string $content, string $filename)
	{
		if (! class_exists(ZipArchive::class)) {
			throw new RuntimeException('The PHP ZIP extension is required to preview Office files.');
		}

		$temporaryFile = tempnam(sys_get_temp_dir(), 'kanboard-office-');
		if ($temporaryFile === false) {
			throw new RuntimeException('Unable to create a temporary file for Office preview.');
		}

		try {
			if (file_put_contents($temporaryFile, $content) === false) {
				throw new RuntimeException('Unable to write the temporary Office preview file.');
			}

			$archive = new ZipArchive;
			if ($archive->open($temporaryFile) !== true) {
				throw new RuntimeException('The Office file is not a valid ZIP archive.');
			}

			try {
				switch (get_file_extension($filename)) {
					case 'docx':
						return array('type' => 'document', 'paragraphs' => $this->readDocument($archive));
					case 'xlsx':
						return array('type' => 'spreadsheet', 'sheets' => $this->readWorkbook($archive));
					case 'pptx':
						return array('type' => 'presentation', 'slides' => $this->readPresentation($archive));
				}
			} finally {
				$archive->close();
			}
		} finally {
			unlink($temporaryFile);
		}

		throw new RuntimeException('Unsupported Office file format.');
	}

	private function readDocument(ZipArchive $archive)
	{
		$xml = $this->readXml($archive, 'word/document.xml');
		$xpath = new DOMXPath($xml);
		$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
		$paragraphs = array();

		foreach ($xpath->query('//w:body//w:p') as $paragraph) {
			$text = '';
			foreach ($xpath->query('.//w:t | .//w:tab | .//w:br', $paragraph) as $node) {
				$text .= $node->localName === 'tab' ? "\t" : ($node->localName === 'br' ? "\n" : $node->textContent);
			}

			if ($text !== '') {
				$paragraphs[] = $text;
			}
		}

		return $paragraphs;
	}

	private function readWorkbook(ZipArchive $archive)
	{
		$workbook = $this->readXml($archive, 'xl/workbook.xml');
		$relationships = $this->readXml($archive, 'xl/_rels/workbook.xml.rels');
		$workbookXPath = new DOMXPath($workbook);
		$workbookXPath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
		$workbookXPath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
		$relationshipXPath = new DOMXPath($relationships);
		$relationshipXPath->registerNamespace('r', 'http://schemas.openxmlformats.org/package/2006/relationships');
		$targets = array();

		foreach ($relationshipXPath->query('//r:Relationship') as $relationship) {
			$targets[$relationship->getAttribute('Id')] = $this->resolveWorkbookTarget($relationship->getAttribute('Target'));
		}

		$sharedStrings = $this->readSharedStrings($archive);
		$sheets = array();

		foreach ($workbookXPath->query('//x:sheets/x:sheet') as $sheetNode) {
			$relationshipId = $sheetNode->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id');
			if (! isset($targets[$relationshipId])) {
				continue;
			}

			$sheetXml = $this->readXml($archive, $targets[$relationshipId]);
			$sheetXPath = new DOMXPath($sheetXml);
			$sheetXPath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
			$rows = array();

			foreach ($sheetXPath->query('//x:sheetData/x:row') as $rowNode) {
				$row = array();
				foreach ($sheetXPath->query('x:c', $rowNode) as $cell) {
					$column = $this->columnIndex($cell->getAttribute('r'));
					if ($column < 0 || $column >= self::MAX_COLUMNS) {
						continue;
					}

					$row[$column] = $this->readCell($sheetXPath, $cell, $sharedStrings);
				}

				if ($row !== array()) {
					$lastColumn = max(array_keys($row));
					$rowValues = array_fill(0, $lastColumn + 1, '');
					foreach ($row as $column => $value) {
						$rowValues[$column] = $value;
					}
					$rows[] = $rowValues;
				}

				if (count($rows) >= self::MAX_ROWS) {
					break;
				}
			}

			$sheets[] = array('name' => $sheetNode->getAttribute('name'), 'rows' => $rows);
		}

		if ($sheets === array()) {
			throw new RuntimeException('No readable worksheets were found in the XLSX file.');
		}

		return $sheets;
	}

	private function readSharedStrings(ZipArchive $archive)
	{
		$index = $archive->locateName('xl/sharedStrings.xml');
		if ($index === false) {
			return array();
		}

		$xml = $this->readXml($archive, 'xl/sharedStrings.xml');
		$xpath = new DOMXPath($xml);
		$xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
		$strings = array();

		foreach ($xpath->query('//x:si') as $item) {
			$text = '';
			foreach ($xpath->query('.//x:t', $item) as $textNode) {
				$text .= $textNode->textContent;
			}
			$strings[] = $text;
		}

		return $strings;
	}

	private function readCell(DOMXPath $xpath, \DOMElement $cell, array $sharedStrings)
	{
		if ($cell->getAttribute('t') === 'inlineStr') {
			$text = '';
			foreach ($xpath->query('.//x:is/x:t', $cell) as $textNode) {
				$text .= $textNode->textContent;
			}
			return $text;
		}

		$value = $xpath->query('x:v', $cell)->item(0);
		if ($value === null) {
			return '';
		}

		if ($cell->getAttribute('t') === 's') {
			$index = (int) $value->textContent;
			return isset($sharedStrings[$index]) ? $sharedStrings[$index] : '';
		}

		return $value->textContent;
	}

	private function columnIndex(string $reference)
	{
		if (! preg_match('/^([A-Z]+)/', $reference, $matches)) {
			return -1;
		}

		$index = 0;
		foreach (str_split($matches[1]) as $letter) {
			$index = $index * 26 + ord($letter) - ord('A') + 1;
		}

		return $index - 1;
	}

	private function resolveWorkbookTarget(string $target)
	{
		$parts = array();
		$path = str_starts_with($target, '/') ? ltrim($target, '/') : 'xl/'.$target;

		foreach (explode('/', $path) as $part) {
			if ($part === '' || $part === '.') {
				continue;
			}
			if ($part === '..') {
				if ($parts === array()) {
					throw new RuntimeException('Invalid worksheet path in XLSX file.');
				}
				array_pop($parts);
			} else {
				$parts[] = $part;
			}
		}

		$resolved = implode('/', $parts);
		if (! str_starts_with($resolved, 'xl/')) {
			throw new RuntimeException('Invalid worksheet path in XLSX file.');
		}

		return $resolved;
	}

	private function readPresentation(ZipArchive $archive)
	{
		$slideFiles = array();
		for ($index = 0; $index < $archive->numFiles; $index++) {
			$name = $archive->getNameIndex($index);
			if (preg_match('!^ppt/slides/slide([0-9]+)\.xml$!', $name, $matches)) {
				$slideFiles[(int) $matches[1]] = $name;
			}
		}

		if ($slideFiles === array()) {
			throw new RuntimeException('No readable slides were found in the PPTX file.');
		}

		ksort($slideFiles);
		$slides = array();
		foreach ($slideFiles as $number => $filename) {
			$xml = $this->readXml($archive, $filename);
			$xpath = new DOMXPath($xml);
			$xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
			$paragraphs = array();

			foreach ($xpath->query('//a:p') as $paragraph) {
				$text = '';
				foreach ($xpath->query('.//a:t', $paragraph) as $textNode) {
					$text .= $textNode->textContent;
				}
				if ($text !== '') {
					$paragraphs[] = $text;
				}
			}

			$slides[] = array('number' => $number, 'paragraphs' => $paragraphs);
		}

		return $slides;
	}

	private function readXml(ZipArchive $archive, string $filename)
	{
		$index = $archive->locateName($filename);
		$stats = $index === false ? false : $archive->statIndex($index);
		if ($stats === false || $stats['size'] > self::MAX_XML_SIZE) {
			throw new RuntimeException('Invalid or oversized Office document XML: '.$filename);
		}

		$content = $archive->getFromIndex($index);
		if ($content === false || stripos($content, '<!DOCTYPE') !== false) {
			throw new RuntimeException('Invalid or oversized Office document XML: '.$filename);
		}

		$document = new DOMDocument;
		$document->resolveExternals = false;
		$document->substituteEntities = false;
		$previousErrorSetting = libxml_use_internal_errors(true);
		$loaded = $document->loadXML($content, LIBXML_NONET | LIBXML_COMPACT);
		libxml_clear_errors();
		libxml_use_internal_errors($previousErrorSetting);

		if (! $loaded) {
			throw new RuntimeException('Unable to parse Office document XML: '.$filename);
		}

		return $document;
	}
}
