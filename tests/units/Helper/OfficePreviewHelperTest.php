<?php

namespace KanboardTests\units\Helper;

use Kanboard\Helper\OfficePreviewHelper;
use KanboardTests\units\Base;
use RuntimeException;
use ZipArchive;

class OfficePreviewHelperTest extends Base
{
	public function testRenderDocxParagraphs()
	{
		$content = $this->createArchive(array(
			'word/document.xml' => '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p><w:r><w:t>Local</w:t></w:r><w:r><w:t> preview</w:t></w:r></w:p></w:body></w:document>',
		));

		$preview = (new OfficePreviewHelper($this->container))->render($content, 'document.docx');

		$this->assertSame('document', $preview['type']);
		$this->assertSame(array('Local preview'), $preview['paragraphs']);
	}

	public function testRenderXlsxSheetsAndPreserveEmptyColumns()
	{
		$content = $this->createArchive(array(
			'xl/workbook.xml' => '<x:workbook xmlns:x="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><x:sheets><x:sheet name="Summary" sheetId="1" r:id="rId1"/></x:sheets></x:workbook>',
			'xl/_rels/workbook.xml.rels' => '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Target="worksheets/sheet1.xml" Type="worksheet"/></Relationships>',
			'xl/sharedStrings.xml' => '<x:sst xmlns:x="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><x:si><x:t>First</x:t></x:si><x:si><x:t>Third</x:t></x:si></x:sst>',
			'xl/worksheets/sheet1.xml' => '<x:worksheet xmlns:x="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><x:sheetData><x:row r="1"><x:c r="A1" t="s"><x:v>0</x:v></x:c><x:c r="C1" t="s"><x:v>1</x:v></x:c></x:row></x:sheetData></x:worksheet>',
		));

		$preview = (new OfficePreviewHelper($this->container))->render($content, 'workbook.xlsx');

		$this->assertSame('spreadsheet', $preview['type']);
		$this->assertSame('Summary', $preview['sheets'][0]['name']);
		$this->assertSame(array('First', '', 'Third'), $preview['sheets'][0]['rows'][0]);
	}

	public function testRenderPptxSlidesInNumericalOrder()
	{
		$content = $this->createArchive(array(
			'ppt/slides/slide2.xml' => '<p:sld xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:p><a:r><a:t>Second</a:t></a:r></a:p></p:sld>',
			'ppt/slides/slide1.xml' => '<p:sld xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:p><a:r><a:t>First</a:t></a:r></a:p></p:sld>',
		));

		$preview = (new OfficePreviewHelper($this->container))->render($content, 'presentation.pptx');

		$this->assertSame('presentation', $preview['type']);
		$this->assertSame(array(1, 2), array_column($preview['slides'], 'number'));
		$this->assertSame(array('First'), $preview['slides'][0]['paragraphs']);
		$this->assertSame(array('Second'), $preview['slides'][1]['paragraphs']);
	}

	public function testRejectInvalidOfficeArchive()
	{
		$this->expectException(RuntimeException::class);
		(new OfficePreviewHelper($this->container))->render('not a zip file', 'document.docx');
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
