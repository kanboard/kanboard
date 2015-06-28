<?php

require_once __DIR__.'/Base.php';

use Core\Lexer;

class LexerTest extends Base
{
    public function testAssigneeQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'assignee:', 'token' => 'T_ASSIGNEE'), array('match' => 'me', 'token' => 'T_STRING')),
            $lexer->tokenize('assignee:me')
        );

        $this->assertEquals(
            array(array('match' => 'assignee:', 'token' => 'T_ASSIGNEE'), array('match' => 'everybody', 'token' => 'T_STRING')),
            $lexer->tokenize('assignee:everybody')
        );

        $this->assertEquals(
            array(array('match' => 'assignee:', 'token' => 'T_ASSIGNEE'), array('match' => 'nobody', 'token' => 'T_STRING')),
            $lexer->tokenize('assignee:nobody')
        );

        $this->assertEquals(
            array('T_ASSIGNEE' => array('nobody')),
            $lexer->map($lexer->tokenize('assignee:nobody'))
        );

        $this->assertEquals(
            array('T_ASSIGNEE' => array('John Doe', 'me')),
            $lexer->map($lexer->tokenize('assignee:"John Doe" assignee:me'))
        );
    }

    public function testColorQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'color:', 'token' => 'T_COLOR'), array('match' => 'Blue', 'token' => 'T_STRING')),
            $lexer->tokenize('color:Blue')
        );

        $this->assertEquals(
            array(array('match' => 'color:', 'token' => 'T_COLOR'), array('match' => 'Dark Grey', 'token' => 'T_STRING')),
            $lexer->tokenize('color:"Dark Grey"')
        );

        $this->assertEquals(
            array('T_COLOR' => array('Blue')),
            $lexer->map($lexer->tokenize('color:Blue'))
        );

        $this->assertEquals(
            array('T_COLOR' => array('Dark Grey')),
            $lexer->map($lexer->tokenize('color:"Dark Grey"'))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('color: '))
        );
    }

    public function testDueDateQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'due:', 'token' => 'T_DUE'), array('match' => '2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('due:2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'due:', 'token' => 'T_DUE'), array('match' => '<2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('due:<2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'due:', 'token' => 'T_DUE'), array('match' => '>2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('due:>2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'due:', 'token' => 'T_DUE'), array('match' => '<=2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('due:<=2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'due:', 'token' => 'T_DUE'), array('match' => '>=2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('due:>=2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'due:', 'token' => 'T_DUE'), array('match' => 'yesterday', 'token' => 'T_DATE')),
            $lexer->tokenize('due:yesterday')
        );

        $this->assertEquals(
            array(array('match' => 'due:', 'token' => 'T_DUE'), array('match' => 'tomorrow', 'token' => 'T_DATE')),
            $lexer->tokenize('due:tomorrow')
        );

        $this->assertEquals(
            array(),
            $lexer->tokenize('due:#2015-05-01')
        );

        $this->assertEquals(
            array(),
            $lexer->tokenize('due:01-05-1024')
        );

        $this->assertEquals(
            array('T_DUE' => '2015-05-01'),
            $lexer->map($lexer->tokenize('due:2015-05-01'))
        );

        $this->assertEquals(
            array('T_DUE' => '<2015-05-01'),
            $lexer->map($lexer->tokenize('due:<2015-05-01'))
        );

        $this->assertEquals(
            array('T_DUE' => 'today'),
            $lexer->map($lexer->tokenize('due:today'))
        );
    }

    public function testMultipleCriterias()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array('T_COLOR' => array('Dark Grey'), 'T_ASSIGNEE' => array('Fred G'), 'T_TITLE' => 'my task title'),
            $lexer->map($lexer->tokenize('color:"Dark Grey" assignee:"Fred G" my task title'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'my title', 'T_COLOR' => array('yellow')),
            $lexer->map($lexer->tokenize('my title color:yellow'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'my title', 'T_DUE' => '2015-04-01'),
            $lexer->map($lexer->tokenize('my title due:2015-04-01'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'awesome', 'T_DUE' => '<=2015-04-01'),
            $lexer->map($lexer->tokenize('due:<=2015-04-01 awesome'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'awesome', 'T_DUE' => 'today'),
            $lexer->map($lexer->tokenize('due:today awesome'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'my title', 'T_COLOR' => array('yellow'), 'T_DUE' => '2015-04-01'),
            $lexer->map($lexer->tokenize('my title color:yellow due:2015-04-01'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'my title', 'T_COLOR' => array('yellow'), 'T_DUE' => '2015-04-01', 'T_ASSIGNEE' => array('John Doe')),
            $lexer->map($lexer->tokenize('my title color:yellow due:2015-04-01 assignee:"John Doe"'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'my title'),
            $lexer->map($lexer->tokenize('my title color:'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'my title'),
            $lexer->map($lexer->tokenize('my title color:assignee:'))
        );

        $this->assertEquals(
            array('T_TITLE' => 'my title'),
            $lexer->map($lexer->tokenize('my title '))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('color:assignee:'))
        );
    }
}
