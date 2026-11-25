<?php

namespace Kanboard\Helper;

use DOMDocument;
use DOMXPath;
use Kanboard\Core\Base;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpWord\IOFactory;
use RuntimeException;
use ZipArchive;

class ServerOfficePreviewHelper extends Base
{
	public const MAX_FILE_SIZE = 20971520;
	private const MAX_ARCHIVE_SIZE = 52428800;
	private const MAX_ARCHIVE_ENTRY_SIZE = 16777216;
	private const MAX_ARCHIVE_ENTRIES = 5000;
	private const MAX_ROWS = 500;
	private const MAX_COLUMNS = 30;
	private const MAX_SHEETS = 10;
	private const MAX_SLIDES = 500;
	private const MAX_PARAGRAPHS = 5000;

	public function render(string $content, string $filename): array
	{
		if ($content === '') {
			throw new RuntimeException('The Office file is empty.');
		}

		if (strlen($content) > self::MAX_FILE_SIZE) {
			throw new RuntimeException('Office preview file exceeds the maximum size.');
		}

		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$requiredEntry = $this->getRequiredEntry($extension);
		$temporaryFile = tempnam(sys_get_temp_dir(), 'kanboard-office-');
		if ($temporaryFile === false) {
			throw new RuntimeException('Unable to create a temporary Office preview file.');
		}

		try {
			if (file_put_contents($temporaryFile, $content, LOCK_EX) !== strlen($content)) {
				throw new RuntimeException('Unable to write the temporary Office preview file.');
			}
			$this->validateArchive($temporaryFile, $requiredEntry);

			switch ($extension) {
				case 'docx':
					return $this->readDocument($temporaryFile);
				case 'xlsx':
					return $this->readWorkbook($temporaryFile);
				case 'pptx':
					return $this->readPresentation($temporaryFile);
				default:
					throw new RuntimeException('Unsupported Office file format.');
			}
		} finally {
			if (is_file($temporaryFile) && ! unlink($temporaryFile)) {
				$this->logger->warning('Unable to remove temporary Office preview file: '.$temporaryFile);
			}
		}
	}

	private function getRequiredEntry(string $extension): string
	{
		switch ($extension) {
			case 'docx':
				return 'word/document.xml';
			case 'xlsx':
				return 'xl/workbook.xml';
			case 'pptx':
				return 'ppt/presentation.xml';
			default:
				throw new RuntimeException('Unsupported Office file format.');
		}
	}

	private function validateArchive(string $filename, string $requiredEntry): void
	{
		$archive = new ZipArchive;
		if ($archive->open($filename) !== true) {
			throw new RuntimeException('The Office file is not a valid ZIP archive.');
		}

		try {
			if ($archive->locateName($requiredEntry) === false) {
				throw new RuntimeException('The Office file is missing required document data.');
			}
			if ($archive->numFiles > self::MAX_ARCHIVE_ENTRIES) {
				throw new RuntimeException('The Office file contains too many archive entries.');
			}
			$totalSize = 0;
			for ($index = 0; $index < $archive->numFiles; $index++) {
				$stats = $archive->statIndex($index);
				if ($stats === false || $stats['size'] > self::MAX_ARCHIVE_ENTRY_SIZE) {
					throw new RuntimeException('The Office file contains an oversized archive entry.');
				}
				$totalSize += $stats['size'];
				if ($totalSize > self::MAX_ARCHIVE_SIZE) {
					throw new RuntimeException('The Office file expands beyond the preview size limit.');
				}
				$name = $archive->getNameIndex($index);
				if (preg_match('/\.xml$/i', $name)) {
					$xml = $archive->getFromIndex($index);
					if ($xml === false || stripos($xml, '<!DOCTYPE') !== false) {
						throw new RuntimeException('The Office file contains invalid XML.');
					}
				}
			}
		} finally {
			$archive->close();
		}
	}

	private function readDocument(string $filename): array
	{
		$reader = IOFactory::createReader('Word2007');
		$reader->setImageLoading(false);
		$document = $reader->load($filename);
		$paragraphs = [];
		$truncated = false;
		foreach ($document->getSections() as $section) {
			foreach ($section->getElements() as $element) {
				$text = $this->getElementText($element);
				if ($text !== '') {
					if (count($paragraphs) === self::MAX_PARAGRAPHS) {
						$truncated = true;
						break 2;
					}
					$paragraphs[] = $text;
				}
			}
		}

		return [
			'type' => 'document',
			'paragraphs' => $paragraphs,
			'truncated' => $truncated,
		];
	}

	private function getElementText($element): string
	{
		if (method_exists($element, 'getText')) {
			$text = $element->getText();
			if (is_string($text)) {
				return $text;
			}
			if (is_object($text) && $text !== $element) {
				return $this->getElementText($text);
			}
		}
		foreach (['getElements', 'getRows', 'getCells'] as $method) {
			if (method_exists($element, $method)) {
				$text = '';
				foreach ($element->$method() as $child) {
					$text .= $this->getElementText($child);
				}
				return $text;
			}
		}
		return '';
	}

	private function readWorkbook(string $filename): array
	{
		$reader = new Xlsx;
		$reader->setReadDataOnly(true);
		$reader->setReadEmptyCells(false);
		$reader->setAllowExternalImages(false);
		$reader->setReadFilter(new class(self::MAX_ROWS, self::MAX_COLUMNS) implements IReadFilter {
			private int $maxRows;
			private int $maxColumns;

			public function __construct(int $maxRows, int $maxColumns)
			{
				$this->maxRows = $maxRows;
				$this->maxColumns = $maxColumns;
			}

			public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
			{
				if ($row < 1 || $row > $this->maxRows || ! preg_match('/^[A-Z]{1,3}$/i', $columnAddress)) {
					return false;
				}
				return Coordinate::columnIndexFromString($columnAddress) <= $this->maxColumns;
			}
		});

		$worksheetInfo = [];
		foreach ($reader->listWorksheetInfo($filename) as $info) {
			$worksheetInfo[$info['worksheetName']] = $info;
		}

		$spreadsheet = $reader->load($filename);
		$sheets = [];
		$sheetCount = min($spreadsheet->getSheetCount(), self::MAX_SHEETS);
		for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
			$worksheet = $spreadsheet->getSheet($sheetIndex);
			if (! isset($worksheetInfo[$worksheet->getTitle()])) {
				throw new RuntimeException('Unable to read worksheet dimensions.');
			}
			$highestRow = (int) $worksheetInfo[$worksheet->getTitle()]['totalRows'];
			$highestColumn = (int) $worksheetInfo[$worksheet->getTitle()]['totalColumns'];
			$lastRow = min(max($highestRow, 0), self::MAX_ROWS);
			$lastColumn = min(max($highestColumn, 0), self::MAX_COLUMNS);
			$rows = [];

			for ($rowIndex = 1; $rowIndex <= $lastRow; $rowIndex++) {
				$row = [];
				for ($columnIndex = 1; $columnIndex <= $lastColumn; $columnIndex++) {
					$value = $worksheet->getCell([$columnIndex, $rowIndex])->getValue();
					if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
						$value = $value->getPlainText();
					}
					$row[] = is_scalar($value) ? (string) $value : '';
				}
				$rows[] = $row;
			}

			$sheets[] = [
				'name' => $worksheet->getTitle(),
				'rows' => $rows,
				'truncated' => $highestRow > self::MAX_ROWS || $highestColumn > self::MAX_COLUMNS,
			];
		}
		if ($sheets === []) {
			throw new RuntimeException('No readable worksheets were found in this file.');
		}

		return [
			'type' => 'spreadsheet',
			'sheets' => $sheets,
			'truncated' => $spreadsheet->getSheetCount() > self::MAX_SHEETS,
		];
	}

	private function readPresentation(string $filename): array
	{
		$archive = new ZipArchive;
		if ($archive->open($filename) !== true) {
			throw new RuntimeException('The presentation is not a valid ZIP archive.');
		}
		try {
			$slideFiles = [];
			for ($index = 0; $index < $archive->numFiles; $index++) {
				$name = $archive->getNameIndex($index);
				if (preg_match('!^ppt/slides/slide([0-9]+)\.xml$!', $name, $matches)) {
					$slideFiles[(int) $matches[1]] = $name;
				}
			}
			if ($slideFiles === []) {
				throw new RuntimeException('No readable slides were found in this presentation.');
			}
			ksort($slideFiles);
			$slides = [];
			foreach (array_slice($slideFiles, 0, self::MAX_SLIDES, true) as $number => $slideFile) {
				$xml = $this->readXml($archive, $slideFile);
				$xpath = new DOMXPath($xml);
				$xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
				$paragraphs = [];
				foreach ($xpath->query('//a:p') as $paragraph) {
					$text = '';
					foreach ($xpath->query('.//a:t', $paragraph) as $textNode) {
						$text .= $textNode->textContent;
					}
					if ($text !== '') {
						$paragraphs[] = $text;
					}
				}
				$slides[] = ['number' => $number, 'paragraphs' => $paragraphs];
			}
			return [
				'type' => 'presentation',
				'slides' => $slides,
				'truncated' => count($slideFiles) > self::MAX_SLIDES,
			];
		} finally {
			$archive->close();
		}
	}

	private function readXml(ZipArchive $archive, string $filename): DOMDocument
	{
		$content = $archive->getFromName($filename);
		if ($content === false || stripos($content, '<!DOCTYPE') !== false) {
			throw new RuntimeException('The presentation contains invalid slide XML.');
		}
		$document = new DOMDocument;
		$document->resolveExternals = false;
		$document->substituteEntities = false;
		$previousErrorSetting = libxml_use_internal_errors(true);
		try {
			$loaded = $document->loadXML($content, LIBXML_NONET | LIBXML_COMPACT);
		} finally {
			libxml_clear_errors();
			libxml_use_internal_errors($previousErrorSetting);
		}
		if (! $loaded) {
			throw new RuntimeException('Unable to parse a presentation slide.');
		}
		return $document;
	}
}
