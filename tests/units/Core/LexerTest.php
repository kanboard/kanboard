<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Lexer;

class LexerTest extends Base
{
    public function testSwimlaneQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'swimlane:', 'token' => 'T_SWIMLANE'), array('match' => 'Version 42', 'token' => 'T_STRING')),
            $lexer->tokenize('swimlane:"Version 42"')
        );

        $this->assertEquals(
            array(array('match' => 'swimlane:', 'token' => 'T_SWIMLANE'), array('match' => 'v3', 'token' => 'T_STRING')),
            $lexer->tokenize('swimlane:v3')
        );

        $this->assertEquals(
            array('T_SWIMLANE' => array('v3')),
            $lexer->map($lexer->tokenize('swimlane:v3'))
        );

        $this->assertEquals(
            array('T_SWIMLANE' => array('Version 42', 'v3')),
            $lexer->map($lexer->tokenize('swimlane:"Version 42" swimlane:v3'))
        );
    }

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

    public function testCategoryQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'category:', 'token' => 'T_CATEGORY'), array('match' => 'Feature Request', 'token' => 'T_STRING')),
            $lexer->tokenize('category:"Feature Request"')
        );

        $this->assertEquals(
            array('T_CATEGORY' => array('Feature Request')),
            $lexer->map($lexer->tokenize('category:"Feature Request"'))
        );

        $this->assertEquals(
            array('T_CATEGORY' => array('Feature Request', 'Bug')),
            $lexer->map($lexer->tokenize('category:"Feature Request" category:Bug'))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('category: '))
        );
    }

    public function testColumnQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'column:', 'token' => 'T_COLUMN'), array('match' => 'Feature Request', 'token' => 'T_STRING')),
            $lexer->tokenize('column:"Feature Request"')
        );

        $this->assertEquals(
            array('T_COLUMN' => array('Feature Request')),
            $lexer->map($lexer->tokenize('column:"Feature Request"'))
        );

        $this->assertEquals(
            array('T_COLUMN' => array('Feature Request', 'Bug')),
            $lexer->map($lexer->tokenize('column:"Feature Request" column:Bug'))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('column: '))
        );
    }

    public function testProjectQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'project:', 'token' => 'T_PROJECT'), array('match' => 'My project', 'token' => 'T_STRING')),
            $lexer->tokenize('project:"My project"')
        );

        $this->assertEquals(
            array('T_PROJECT' => array('My project')),
            $lexer->map($lexer->tokenize('project:"My project"'))
        );

        $this->assertEquals(
            array('T_PROJECT' => array('My project', 'plop')),
            $lexer->map($lexer->tokenize('project:"My project" project:plop'))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('project: '))
        );
    }

    public function testStatusQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'status:', 'token' => 'T_STATUS'), array('match' => 'open', 'token' => 'T_STRING')),
            $lexer->tokenize('status:open')
        );

        $this->assertEquals(
            array(array('match' => 'status:', 'token' => 'T_STATUS'), array('match' => 'closed', 'token' => 'T_STRING')),
            $lexer->tokenize('status:closed')
        );

        $this->assertEquals(
            array('T_STATUS' => 'open'),
            $lexer->map($lexer->tokenize('status:open'))
        );

        $this->assertEquals(
            array('T_STATUS' => 'closed'),
            $lexer->map($lexer->tokenize('status:closed'))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('status: '))
        );
    }

    public function testReferenceQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'ref:', 'token' => 'T_REFERENCE'), array('match' => '123', 'token' => 'T_STRING')),
            $lexer->tokenize('ref:123')
        );

        $this->assertEquals(
            array(array('match' => 'reference:', 'token' => 'T_REFERENCE'), array('match' => '456', 'token' => 'T_STRING')),
            $lexer->tokenize('reference:456')
        );

        $this->assertEquals(
            array('T_REFERENCE' => '123'),
            $lexer->map($lexer->tokenize('reference:123'))
        );

        $this->assertEquals(
            array('T_REFERENCE' => '456'),
            $lexer->map($lexer->tokenize('ref:456'))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('ref: '))
        );
    }

    public function testDescriptionQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'description:', 'token' => 'T_DESCRIPTION'), array('match' => 'my text search', 'token' => 'T_STRING')),
            $lexer->tokenize('description:"my text search"')
        );

        $this->assertEquals(
            array('T_DESCRIPTION' => 'my text search'),
            $lexer->map($lexer->tokenize('description:"my text search"'))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('description: '))
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

    public function testModifiedQuery()
    {
        $lexer = new Lexer;

        $this->assertEquals(
            array(array('match' => 'modified:', 'token' => 'T_UPDATED'), array('match' => '2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('modified:2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'modified:', 'token' => 'T_UPDATED'), array('match' => '<2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('modified:<2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'modified:', 'token' => 'T_UPDATED'), array('match' => '>2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('modified:>2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'updated:', 'token' => 'T_UPDATED'), array('match' => '<=2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('updated:<=2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'updated:', 'token' => 'T_UPDATED'), array('match' => '>=2015-05-01', 'token' => 'T_DATE')),
            $lexer->tokenize('updated:>=2015-05-01')
        );

        $this->assertEquals(
            array(array('match' => 'updated:', 'token' => 'T_UPDATED'), array('match' => 'yesterday', 'token' => 'T_DATE')),
            $lexer->tokenize('updated:yesterday')
        );

        $this->assertEquals(
            array(array('match' => 'updated:', 'token' => 'T_UPDATED'), array('match' => 'tomorrow', 'token' => 'T_DATE')),
            $lexer->tokenize('updated:tomorrow')
        );

        $this->assertEquals(
            array(),
            $lexer->tokenize('updated:#2015-05-01')
        );

        $this->assertEquals(
            array(),
            $lexer->tokenize('modified:01-05-1024')
        );

        $this->assertEquals(
            array('T_UPDATED' => '2015-05-01'),
            $lexer->map($lexer->tokenize('modified:2015-05-01'))
        );

        $this->assertEquals(
            array('T_UPDATED' => '<2015-05-01'),
            $lexer->map($lexer->tokenize('modified:<2015-05-01'))
        );

        $this->assertEquals(
            array('T_UPDATED' => 'today'),
            $lexer->map($lexer->tokenize('modified:today'))
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
            array('T_TITLE' => '#123'),
            $lexer->map($lexer->tokenize('#123'))
        );

        $this->assertEquals(
            array(),
            $lexer->map($lexer->tokenize('color:assignee:'))
        );
    }
}
