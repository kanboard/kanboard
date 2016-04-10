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
        $lexer->addToken("/^(date:)/", 'T_DATE');

        $expected = array(
            'T_DATE' => array('today'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:today something else'));
    }

    public function testTokenizeWithIsoDate()
    {
        $lexer = new Lexer();
        $lexer->addToken("/^(date:)/", 'T_DATE');

        $expected = array(
            'T_DATE' => array('<=2016-01-01'),
        );

        $this->assertSame($expected, $lexer->tokenize('date:<=2016-01-01 something else'));
    }
}
