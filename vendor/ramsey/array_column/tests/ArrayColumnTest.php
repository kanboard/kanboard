<?php
class ArrayColumnTest extends \PHPUnit_Framework_TestCase
{
    protected $recordSet;
    protected $multiDataTypes;
    protected $numericColumns;
    protected $mismatchedColumns;

    protected function setUp()
    {
        $this->recordSet = array(
            array(
                'id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe'
            ),
            array(
                'id' => 2,
                'first_name' => 'Sally',
                'last_name' => 'Smith'
            ),
            array(
                'id' => 3,
                'first_name' => 'Jane',
                'last_name' => 'Jones'
            ),
        );

        $fh = fopen(__FILE__, 'r', true);

        $this->multiDataTypes = array(
            array(
                'id' => 1,
                'value' => new stdClass
            ),
            array(
                'id' => 2,
                'value' => 34.2345
            ),
            array(
                'id' => 3,
                'value' => true
            ),
            array(
                'id' => 4,
                'value' => false
            ),
            array(
                'id' => 5,
                'value' => null
            ),
            array(
                'id' => 6,
                'value' => 1234
            ),
            array(
                'id' => 7,
                'value' => 'Foo'
            ),
            array(
                'id' => 8,
                'value' => $fh
            ),
        );

        $this->numericColumns = array(
            array('aaa', '111'),
            array('bbb', '222'),
            array('ccc', '333', -1 => 'ddd'),
        );

        $this->mismatchedColumns = array(
            array('a' => 'foo', 'b' => 'bar', 'e' => 'bbb'),
            array('a' => 'baz', 'c' => 'qux', 'd' => 'aaa'),
            array('a' => 'eee', 'b' => 'fff', 'e' => 'ggg'),
        );
    }

    public function testFirstNameColumnFromRecordset()
    {
        $expected = array('John', 'Sally', 'Jane');
        $this->assertEquals($expected, array_column($this->recordSet, 'first_name'));
    }

    public function testIdColumnFromRecordset()
    {
        $expected = array(1, 2, 3);
        $this->assertEquals($expected, array_column($this->recordSet, 'id'));
    }

    public function testLastNameColumnKeyedByIdColumnFromRecordset()
    {
        $expected = array(1 => 'Doe', 2 => 'Smith', 3 => 'Jones');
        $this->assertEquals($expected, array_column($this->recordSet, 'last_name', 'id'));
    }

    public function testLastNameColumnKeyedByFirstNameColumnFromRecordset()
    {
        $expected = array('John' => 'Doe', 'Sally' => 'Smith', 'Jane' => 'Jones');
        $this->assertEquals($expected, array_column($this->recordSet, 'last_name', 'first_name'));
    }

    public function testValueColumnWithMultipleDataTypes()
    {
        $expected = array();
        foreach ($this->multiDataTypes as $row) {
            $expected[] = $row['value'];
        }
        $this->assertEquals($expected, array_column($this->multiDataTypes, 'value'));
    }

    public function testValueColumnKeyedByIdWithMultipleDataTypes()
    {
        $expected = array();
        foreach ($this->multiDataTypes as $row) {
            $expected[$row['id']] = $row['value'];
        }
        $this->assertEquals($expected, array_column($this->multiDataTypes, 'value', 'id'));
    }

    public function testNumericColumnKeys1()
    {
        $expected = array('111', '222', '333');
        $this->assertEquals($expected, array_column($this->numericColumns, 1));
    }

    public function testNumericColumnKeys2()
    {
        $expected = array('aaa' => '111', 'bbb' => '222', 'ccc' => '333');
        $this->assertEquals($expected, array_column($this->numericColumns, 1, 0));
    }

    public function testNumericColumnKeys3()
    {
        $expected = array('aaa' => '111', 'bbb' => '222', 'ccc' => '333');
        $this->assertEquals($expected, array_column($this->numericColumns, 1, 0.123));
    }

    public function testNumericColumnKeys4()
    {
        $expected = array(0 => '111', 1 => '222', 'ddd' => '333');
        $this->assertEquals($expected, array_column($this->numericColumns, 1, -1));
    }

    public function testFailureToFindColumn1()
    {
        $this->assertEquals(array(), array_column($this->numericColumns, 2));
    }

    public function testFailureToFindColumn2()
    {
        $this->assertEquals(array(), array_column($this->numericColumns, 'foo'));
    }

    public function testFailureToFindColumn3()
    {
        $expected = array('aaa', 'bbb', 'ccc');
        $this->assertEquals($expected, array_column($this->numericColumns, 0, 'foo'));
    }

    public function testFailureToFindColumn4()
    {
        $this->assertEquals(array(), array_column($this->numericColumns, 3.14));
    }

    public function testSingleDimensionalArray()
    {
        $singleDimension = array('foo', 'bar', 'baz');
        $this->assertEquals(array(), array_column($singleDimension, 1));
    }

    public function testMismatchedColumns1()
    {
        $expected = array('qux');
        $this->assertEquals($expected, array_column($this->mismatchedColumns, 'c'));
    }

    public function testMismatchedColumns2()
    {
        $expected = array('baz' => 'qux');
        $this->assertEquals($expected, array_column($this->mismatchedColumns, 'c', 'a'));
    }

    public function testMismatchedColumns3()
    {
        $expected = array('foo', 'aaa' => 'baz', 'eee');
        $this->assertEquals($expected, array_column($this->mismatchedColumns, 'a', 'd'));
    }

    public function testMismatchedColumns4()
    {
        $expected = array('bbb' => 'foo', 'baz', 'ggg' => 'eee');
        $this->assertEquals($expected, array_column($this->mismatchedColumns, 'a', 'e'));
    }

    public function testMismatchedColumns5()
    {
        $expected = array('bar', 'fff');
        $this->assertEquals($expected, array_column($this->mismatchedColumns, 'b'));
    }

    public function testMismatchedColumns6()
    {
        $expected = array('foo' => 'bar', 'eee' => 'fff');
        $this->assertEquals($expected, array_column($this->mismatchedColumns, 'b', 'a'));
    }

    public function testObjectConvertedToString()
    {
        $f = new Foo();
        $b = new Bar();
        $this->assertEquals(array('Doe', 'Smith', 'Jones'), array_column($this->recordSet, $f));
        $this->assertEquals(array('John' => 'Doe', 'Sally' => 'Smith', 'Jane' => 'Jones'), array_column($this->recordSet, $f, $b));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage array_column() expects at least 2 parameters, 0 given
     */
    public function testFunctionWithZeroArgs()
    {
        $foo = array_column();
    }

    public function testFunctionWithZeroArgsReturnValue()
    {
        $foo = @array_column();
        $this->assertNull($foo);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage array_column() expects at least 2 parameters, 1 given
     */
    public function testFunctionWithOneArg()
    {
        $foo = array_column(array());
    }

    public function testFunctionWithOneArgReturnValue()
    {
        $foo = @array_column(array());
        $this->assertNull($foo);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage array_column() expects parameter 1 to be array, string given
     */
    public function testFunctionWithStringAsFirstArg()
    {
        $foo = array_column('foo', 0);
    }

    public function testFunctionWithStringAsFirstArgReturnValue()
    {
        $foo = @array_column('foo', 0);
        $this->assertNull($foo);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage array_column() expects parameter 1 to be array, integer given
     */
    public function testFunctionWithIntAsFirstArg()
    {
        $foo = array_column(1, 'foo');
    }

    public function testFunctionWithIntAsFirstArgReturnValue()
    {
        $foo = @array_column(1, 'foo');
        $this->assertNull($foo);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage array_column(): The column key should be either a string or an integer
     */
    public function testFunctionWithColumnKeyAsBool()
    {
        $foo = array_column(array(), true);
    }

    public function testFunctionWithColumnKeyAsBoolReturnValue()
    {
        $foo = @array_column(array(), true);
        $this->assertFalse($foo);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage array_column(): The column key should be either a string or an integer
     */
    public function testFunctionWithColumnKeyAsArray()
    {
        $foo = array_column(array(), array());
    }

    public function testFunctionWithColumnKeyAsArrayReturnValue()
    {
        $foo = @array_column(array(), array());
        $this->assertFalse($foo);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage array_column(): The index key should be either a string or an integer
     */
    public function testFunctionWithIndexKeyAsBool()
    {
        $foo = array_column(array(), 'foo', true);
    }

    public function testFunctionWithIndexKeyAsBoolReturnValue()
    {
        $foo = @array_column(array(), 'foo', true);
        $this->assertFalse($foo);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage array_column(): The index key should be either a string or an integer
     */
    public function testFunctionWithIndexKeyAsArray()
    {
        $foo = array_column(array(), 'foo', array());
    }

    public function testFunctionWithIndexKeyAsArrayReturnValue()
    {
        $foo = @array_column(array(), 'foo', array());
        $this->assertFalse($foo);
    }

    /**
     * @link https://bugs.php.net/bug.php?id=64493
     */
    public function testBugRequest64493()
    {
        // Array from Bug Request #64493 test script
        $rows = array(
            456 => array('id' => '3', 'title' => 'Foo', 'date' => '2013-03-25'),
            457 => array('id' => '5', 'title' => 'Bar', 'date' => '2012-05-20'),
        );

        // pass null as second parameter to get back all columns indexed by third parameter
        $expected1 = array(
            3 => array('id' => '3', 'title' => 'Foo', 'date' => '2013-03-25'),
            5 => array('id' => '5', 'title' => 'Bar', 'date' => '2012-05-20'),
        );
        $this->assertEquals($expected1, array_column($rows, null, 'id'));

        // pass null as second parameter and bogus third param to get back zero-indexed array of all columns
        $expected2 = array(
            array('id' => '3', 'title' => 'Foo', 'date' => '2013-03-25'),
            array('id' => '5', 'title' => 'Bar', 'date' => '2012-05-20'),
        );
        $this->assertEquals($expected2, array_column($rows, null, 'foo'));

        // pass null as second parameter and no third param to get back array_values(input) (same as $expected2)
        $this->assertEquals($expected2, array_column($rows, null));
    }

    public function testObjectCast()
    {
        $columnKey = new ColumnKeyClass();
        $indexKey = new IndexKeyClass();
        $value = new ValueClass();

        $records = array(
            array(
                'id' => $value,
                'first_name' => 'John',
                'last_name' => 'XXX'
            ),
            array(
                'id' => 3245,
                'first_name' => 'Sally',
                'last_name' => 'Smith'
            ),
        );

        $expected = array(
            2135 => 'John',
            3245 => 'Sally',
        );

        $this->assertEquals($expected, array_column($records, $columnKey, $indexKey));
    }
}

class Foo
{
    public function __toString()
    {
        return 'last_name';
    }
}

class Bar
{
    public function __toString()
    {
        return 'first_name';
    }
}

class ColumnKeyClass
{
    function __toString() { return 'first_name'; }
}

class IndexKeyClass
{
    function __toString() { return 'id'; }
}

class ValueClass
{
    function __toString() { return '2135'; }
}
