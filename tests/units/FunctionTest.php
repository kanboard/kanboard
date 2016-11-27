<?php

require_once __DIR__.'/Base.php';

class FunctionTest extends Base
{
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
}
