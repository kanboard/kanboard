<?php

namespace KanboardTests\units\Helper;

use Kanboard\Helper\ServerOfficePreviewHelper;
use KanboardTests\units\Base;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use RuntimeException;
use ZipArchive;

class OfficePreviewHelperTest extends Base
{
	public function testRenderDocxParagraphs()
	{
		$document = new PhpWord;
		$document->addSection()->addText('Local preview');
		$content = $this->createOfficeFile(function ($filename) use ($document) {
			IOFactory::createWriter($document, 'Word2007')->save($filename);
		});

		$preview = (new ServerOfficePreviewHelper($this->container))->render($content, 'document.docx');

		$this->assertSame('document', $preview['type']);
		$this->assertSame(array('Local preview'), $preview['paragraphs']);
	}

	public function testRenderXlsxSheetsAndPreserveEmptyColumns()
	{
		$spreadsheet = new Spreadsheet;
		$worksheet = $spreadsheet->getActiveSheet();
		$worksheet->setTitle('Summary');
		$worksheet->setCellValue('A1', 'First');
		$worksheet->setCellValue('C1', 'Third');
		$content = $this->createOfficeFile(function ($filename) use ($spreadsheet) {
			(new Xlsx($spreadsheet))->save($filename);
		});

		$preview = (new ServerOfficePreviewHelper($this->container))->render($content, 'workbook.xlsx');

		$this->assertSame('spreadsheet', $preview['type']);
		$this->assertSame('Summary', $preview['sheets'][0]['name']);
		$this->assertSame(array('First', '', 'Third'), $preview['sheets'][0]['rows'][0]);
	}

	public function testRenderPptxSlidesInNumericalOrder()
	{
		$content = $this->createArchive(array(
			'ppt/presentation.xml' => '<p:presentation xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"/>',
			'ppt/slides/slide2.xml' => '<p:sld xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:p><a:r><a:t>Second</a:t></a:r></a:p></p:sld>',
			'ppt/slides/slide1.xml' => '<p:sld xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:p><a:r><a:t>First</a:t></a:r></a:p></p:sld>',
		));

		$preview = (new ServerOfficePreviewHelper($this->container))->render($content, 'presentation.pptx');

		$this->assertSame('presentation', $preview['type']);
		$this->assertSame(array(1, 2), array_column($preview['slides'], 'number'));
		$this->assertSame(array('First'), $preview['slides'][0]['paragraphs']);
		$this->assertSame(array('Second'), $preview['slides'][1]['paragraphs']);
	}

	public function testRejectInvalidOfficeArchive()
	{
		$this->expectException(RuntimeException::class);
		(new ServerOfficePreviewHelper($this->container))->render('not a zip file', 'document.docx');
	}

	public function testRejectEmptyOfficeFile()
	{
		$this->expectException(RuntimeException::class);
		(new ServerOfficePreviewHelper($this->container))->render('', 'empty.docx');
	}

	private function createOfficeFile(callable $write)
	{
		$filename = tempnam(sys_get_temp_dir(), 'kanboard-office-test-');
		if ($filename === false) {
			$this->fail('Unable to create a temporary Office preview file.');
		}

		try {
			$write($filename);
			$content = file_get_contents($filename);
			$this->assertNotFalse($content);
			return $content;
		} finally {
			unlink($filename);
		}
	}

	private function createArchive(array $files)
	{
		$filename = tempnam(sys_get_temp_dir(), 'kanboard-office-test-');
		if ($filename === false) {
			$this->fail('Unable to create a temporary Office test file.');
		}

		try {
			$archive = new ZipArchive;
			$this->assertSame(true, $archive->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE));
			foreach ($files as $path => $content) {
				$this->assertTrue($archive->addFromString($path, $content));
			}
			$this->assertTrue($archive->close());

			$content = file_get_contents($filename);
			$this->assertNotFalse($content);
			return $content;
		} finally {
			unlink($filename);
		}
	}
}
