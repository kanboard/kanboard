<?php

namespace KanboardTests\units\Model;

use Kanboard\Core\Security\Role;
use Kanboard\Model\CommentModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectNotificationModel;
use Kanboard\Model\ProjectNotificationTypeModel;
use Kanboard\Model\TaskModel;
use KanboardTests\units\Base;

class ProjectNotificationTest extends Base
{
    public function testSendNotificationsWithCommentVisibility()
    {
        $projectModel = new ProjectModel($this->container);
        $projectNotificationModel = new ProjectNotificationModel($this->container);
        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));

        $notified = $this->mockNotificationTypes();
        $allTypes = array('webhook', 'activity_stream', 'slack');
        $systemTypes = array('webhook', 'activity_stream');

        $expected = array(
            Role::APP_USER => $allTypes,
            Role::APP_MANAGER => $systemTypes,
            Role::APP_ADMIN => $systemTypes,
        );

        foreach ($expected as $visibility => $types) {
            $notified->exchangeArray(array());
            $projectNotificationModel->sendNotifications(1, CommentModel::EVENT_CREATE, array(
                'task' => array('id' => 1, 'project_id' => 1),
                'comment' => array('id' => 1, 'comment' => 'Comment', 'visibility' => $visibility),
            ));

            $this->assertSame($types, $notified->getArrayCopy(), $visibility);
        }

        $notified->exchangeArray(array());
        $projectNotificationModel->sendNotifications(1, TaskModel::EVENT_CREATE, array('task' => array('id' => 1, 'project_id' => 1)));
        $this->assertSame($allTypes, $notified->getArrayCopy());
    }

    private function mockNotificationTypes()
    {
        $notified = new \ArrayObject();

        $this->container['projectNotificationTypeModel'] = $this
            ->getMockBuilder(ProjectNotificationTypeModel::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('getHiddenTypes', 'getSelectedTypes', 'getType'))
            ->getMock();

        $this->container['projectNotificationTypeModel']
            ->expects($this->atLeastOnce())
            ->method('getHiddenTypes')
            ->willReturn(array('webhook', 'activity_stream'));

        $this->container['projectNotificationTypeModel']
            ->method('getSelectedTypes')
            ->willReturn(array('slack'));

        $this->container['projectNotificationTypeModel']
            ->method('getType')
            ->willReturnCallback(function ($type) use ($notified) {
                return new class($type, $notified) {
                    public function __construct(private string $type, private \ArrayObject $notified)
                    {
                    }

                    public function notifyProject(array $project, $eventName, array $eventData)
                    {
                        $this->notified[] = $this->type;
                    }
                };
            });

        return $notified;
    }
}
