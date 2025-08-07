<?php

namespace KanboardTests\units;

class FunctionTest extends Base
{
    public function testConvertPHPSizeToBytes()
    {
        $this->assertEquals(2097152, convert_php_size_to_bytes('2M'));
        $this->assertEquals(2048, convert_php_size_to_bytes('2 k'));
        $this->assertEquals(0, convert_php_size_to_bytes('0'));
    }

    public function testArrayColumnSum()
    {
        $input = array(
            array(
                'my_column' => 123
            ),
            array(
                'my_column' => 456.7
            ),
            array()
        );

        $this->assertSame(579.7, array_column_sum($input, 'my_column'));
    }

    public function testArrayColumnIndex()
    {
        $input = array(
            array(
                'k1' => 11,
                'k2' => 22,
            ),
            array(
                'k1' => 11,
                'k2' => 55,
            ),
            array(
                'k1' => 33,
                'k2' => 44,
            ),
            array()
        );

        $expected = array(
            11 => array(
                array(
                    'k1' => 11,
                    'k2' => 22,
                ),
                array(
                    'k1' => 11,
                    'k2' => 55,
                )
            ),
            33 => array(
                array(
                    'k1' => 33,
                    'k2' => 44,
                )
            )
        );

        $this->assertSame($expected, array_column_index($input, 'k1'));
    }

    public function testArrayColumnIndexUnique()
    {
        $input = array(
            array(
                'k1' => 11,
                'k2' => 22,
            ),
            array(
                'k1' => 11,
                'k2' => 55,
            ),
            array(
                'k1' => 33,
                'k2' => 44,
            ),
            array()
        );

        $expected = array(
            11 => array(
                'k1' => 11,
                'k2' => 22,
            ),
            33 => array(
                'k1' => 33,
                'k2' => 44,
            )
        );

        $this->assertSame($expected, array_column_index_unique($input, 'k1'));
    }

    public function testArrayMergeRelation()
    {
        $relations = array(
            88 => array(
                'id' => 123,
                'value' => 'test1',
            ),
            99 => array(
                'id' => 456,
                'value' => 'test2',
            ),
            55 => array()
        );

        $input = array(
            array(),
            array(
                'task_id' => 88,
                'title' => 'task1'
            ),
            array(
                'task_id' => 99,
                'title' => 'task2'
            ),
            array(
                'task_id' => 11,
                'title' => 'task3'
            )
        );

        $expected = array(
            array(
                'my_relation' => array(),
            ),
            array(
                'task_id' => 88,
                'title' => 'task1',
                'my_relation' => array(
                    'id' => 123,
                    'value' => 'test1',
                ),
            ),
            array(
                'task_id' => 99,
                'title' => 'task2',
                'my_relation' => array(
                    'id' => 456,
                    'value' => 'test2',
                ),
            ),
            array(
                'task_id' => 11,
                'title' => 'task3',
                'my_relation' => array(),
            )
        );

        array_merge_relation($input, $relations, 'my_relation', 'task_id');

        $this->assertSame($expected, $input);
    }

    public function testSanitizePath()
    {
        // Test empty path
        $this->assertFalse(sanitize_path(''));

        // Test root path
        $this->assertEquals('/', sanitize_path('/'));

        // Test simple absolute path
        $this->assertEquals('/home/user/file.txt', sanitize_path('/home/user/file.txt'));

        // Test path with Windows-style backslashes
        $this->assertEquals('/home/user/file.txt', sanitize_path('\\home\\user\\file.txt'));

        // Test path with multiple slashes
        $this->assertEquals('/home/user/file.txt', sanitize_path('///home//user///file.txt'));

        // Test path with current directory references
        $this->assertEquals('/home/user/file.txt', sanitize_path('/home/./user/./file.txt'));

        // Test path with parent directory navigation
        $this->assertEquals('/home/file.txt', sanitize_path('/home/user/../file.txt'));

        // Test complex path with mixed navigation
        $this->assertEquals('/home/user/documents/file.txt', sanitize_path('/home/user/../user/./documents/../documents/file.txt'));

        // Test path trying to go above root
        $this->assertEquals('/', sanitize_path('/../../..'));

        // Test relative path (should be converted to absolute)
        $currentDir = getcwd();
        $this->assertEquals($currentDir . '/test/file.txt', sanitize_path('test/file.txt'));

        // Test relative path with parent navigation
        $expectedPath = dirname($currentDir) . '/file.txt';
        $this->assertEquals($expectedPath, sanitize_path('../file.txt'));

        // Test path with trailing slash removal
        $this->assertEquals('/home/user', sanitize_path('/home/user/'));

        // Test complex Windows-style path
        $this->assertEquals('/c/Users/John/Documents/file.txt', sanitize_path('\\c\\Users\\John\\..\\John\\Documents\\file.txt'));

        // Test path with only dots and slashes
        $this->assertEquals('/', sanitize_path('/.././../'));

        // Test path with empty components
        $this->assertEquals('/home/user/file.txt', sanitize_path('/home//user///file.txt'));
    }
}
