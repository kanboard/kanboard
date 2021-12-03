<?php

use Kanboard\Formatter\UserMentionFormatter;

require_once __DIR__.'/../Base.php';

class UserMentionFormatterTest extends Base
{
    public function testFormat()
    {
        $userMentionFormatter = new UserMentionFormatter($this->container);
        $users = array(
            array(
                'id' => 1,
                'username' => 'someone',
                'name' => 'Someone',
                'email' => 'test@localhost',
                'avatar_path' => 'avatar_image',
            ),
            array(
                'id' => 2,
                'username' => 'somebody',
                'name' => '',
                'email' => '',
                'avatar_path' => '',
            )
        );

        $expected = array(
            array(
                'value' => 'someone',
                'html' => '<div class="avatar avatar-20 avatar-inline"><img src="?controller=AvatarFileController&amp;action=image&amp;user_id=1&amp;hash=871b0146d6689014b79b878c7b120151&amp;size=20" alt="Someone" title="Someone"></div> someone <small aria-hidden="true">Someone</small>',
            ),
            array(
                'value' => 'somebody',
                'html' => '<div class="avatar avatar-20 avatar-inline"><div class="avatar-letter" style="background-color: rgb(191, 210, 121)" title="somebody" role="img" aria-label="somebody">S</div></div> somebody',
            ),
        );

        $this->assertSame($expected, $userMentionFormatter->withUsers($users)->format());
    }
}
