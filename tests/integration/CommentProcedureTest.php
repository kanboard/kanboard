<?php

require_once __DIR__.'/BaseProcedureTest.php';

class CommentProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test comments';
    private $commentId = 0;

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertCreateComment();
        $this->assertUpdateComment();
        $this->assertGetAllComments();
        $this->assertRemoveComment();
    }

    public function assertCreateComment()
    {
        $this->commentId = $this->app->execute('createComment', array(
            'task_id' => $this->taskId,
            'user_id' => 1,
            'content' => 'foobar',
        ));

        $this->assertNotFalse($this->commentId);
    }

    public function assertGetComment()
    {
        $comment = $this->app->getComment($this->commentId);
        $this->assertNotFalse($comment);
        $this->assertNotEmpty($comment);
        $this->assertEquals(1, $comment['user_id']);
        $this->assertEquals('foobar', $comment['comment']);
        $this->assertEquals($comment['date_creation'], $comment['date_modification']);
    }

    public function assertUpdateComment()
    {
        sleep(1); // Integration test fails because its too fast 
        $this->assertTrue($this->app->execute('updateComment', array(
            'id' => $this->commentId,
            'content' => 'test',
        )));

        $comment = $this->app->getComment($this->commentId);
        $this->assertEquals('test', $comment['comment']);
        $this->assertNotEquals($comment['date_creation'], $comment['date_modification']);
    }

    public function assertGetAllComments()
    {
        $comments = $this->app->getAllComments($this->taskId);
        $this->assertCount(1, $comments);
        $this->assertEquals('test', $comments[0]['comment']);
    }

    public function assertRemoveComment()
    {
        $this->assertTrue($this->app->removeComment($this->commentId));
        $this->assertFalse($this->app->removeComment($this->commentId));
    }
}
