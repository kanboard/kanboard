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
}
