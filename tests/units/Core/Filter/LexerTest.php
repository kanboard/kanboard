<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Filter\Lexer;

class LexerTest extends Base
{
    public function testTokenizeWithNoDefaultToken()
    {
        $lexer = new Lexer();
        $this->assertSame(array(), $lexer->tokenize('This is Kanboard'));
    }

    public function testTokenizeWithDefaultToken()
    {
        $lexer = new Lexer();
        $lexer->setDefaultToken('myDefaultToken');

        $expected = array(
            'myDefaultToken' => array('This is Kanboard'),
        );

        $this->assertSame($expected, $lexer->tokenize('This is Kanboard'));
    }

    public function testTokenizeWithCustomToken()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(assignee:)/", 'T_USER');

        $expected = array(
            'T_USER' => array('admin'),
        );

        $this->assertSame($expected, $lexer->tokenize('assignee:admin something else'));
    }

    public function testTokenizeWithCustomTokenAndDefaultToken()
    {
        $lexer = new Lexer();
        $lexer->setDefaultToken('myDefaultToken');
        $lexer->addToken("/^(assignee:)/", 'T_USER');

        $expected = array(
            'T_USER' => array('admin'),
            'myDefaultToken' => array('something else'),
        );

        $this->assertSame($expected, $lexer->tokenize('assignee:admin something else'));
    }

    public function testTokenizeWithQuotedString()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(assignee:)/", 'T_USER');

        $expected = array(
            'T_USER' => array('Foo Bar'),
        );

        $this->assertSame($expected, $lexer->tokenize('assignee:"Foo Bar" something else'));
    }

    public function testTokenizeWithNumber()
    {
        $lexer = new Lexer();
        $lexer->setDefaultToken('myDefaultToken');

        $expected = array(
            'myDefaultToken' => array('#123'),
        );

        $this->assertSame($expected, $lexer->tokenize('#123'));
    }

    public function testTokenizeWithStringDate()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('today'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:today something else'));
    }

    public function testTokenizeWithStringDateWithSpaces()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('last month'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:"last month" something else'));
    }

    public function testTokenizeWithStringDateWithSpacesAndOperator()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('<=last month'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:<="last month" something else'));

        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('>=next month'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:>="next month" something else'));

        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('<+2 days'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:<"+2 days" something else'));

        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('<-1 hour'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:<"-1 hour" something else'));
    }

    public function testTokenizeWithStringDateAndOperator()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('<=today'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:<=today something else'));

        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('>now'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:>now something else'));

        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('>=now'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:>=now something else'));
    }

    public function testTokenizeWithIsoDate()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_MY_DATE');

        $expected = array(
            'T_MY_DATE' => array('<=2016-01-01'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:<=2016-01-01 something else'));
    }

    public function testTokenizeWithUtf8Letters()
    {
        $lexer = new Lexer();
        $lexer->setDefaultToken('myDefaultToken');

        $expected = array(
            'myDefaultToken' => array('àa éçùe'),
        );

        $this->assertSame($expected, $lexer->tokenize('àa éçùe'));
    }

    public function testTokenizeWithUtf8Numbers()
    {
        $lexer = new Lexer();
        $lexer->setDefaultToken('myDefaultToken');

        $expected = array(
            'myDefaultToken' => array('६Δↈ五一'),
        );

        $this->assertSame($expected, $lexer->tokenize('६Δↈ五一'));
    }

    public function testTokenizeWithMultipleValues()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(tag:)/", 'T_TAG');

        $expected = array(
            'T_TAG' => array('tag 1', 'tag2'),
        );

        $this->assertSame($expected, $lexer->tokenize('tag:"tag 1" tag:tag2'));
    }

    public function testTokenizeWithDash()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(test:)/", 'T_TEST');

        $expected = array(
            'T_TEST' => array('PO-123'),
        );

        $this->assertSame($expected, $lexer->tokenize('test:PO-123'));

        $lexer = new Lexer();
        $lexer->setDefaultToken('myDefaultToken');

        $expected = array(
            'myDefaultToken' => array('PO-123'),
        );

        $this->assertSame($expected, $lexer->tokenize('PO-123'));
    }

    public function testTokenizeWithUnderscore()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(test:)/", 'T_TEST');

        $expected = array(
            'T_TEST' => array('PO_123'),
        );

        $this->assertSame($expected, $lexer->tokenize('test:PO_123'));

        $lexer = new Lexer();
        $lexer->addToken("/^(test:)/", 'T_TEST');
        $lexer->setDefaultToken('myDefaultToken');

        $expected = array(
            'T_TEST' => array('ABC-123'),
            'myDefaultToken' => array('PO_123'),
        );

        $this->assertSame($expected, $lexer->tokenize('test:ABC-123 PO_123'));
    }
}
