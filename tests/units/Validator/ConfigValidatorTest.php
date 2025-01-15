<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\ConfigValidator;

class ConfigValidatorTest extends Base
{
    public function testValidatePasswordModification()
    {
        $configValidator = new ConfigValidator($this->container);

        $result = $configValidator->validate(array('application_url' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_url' => 'http://localhost'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_url' => 'invalid'));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('application_timezone' => 'Europe/Paris'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_timezone' => 'UTC'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_timezone' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_timezone' => 'unknown'));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('application_date_format' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_date_format' => 'd/m/Y'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_date_format' => 'invalid'));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('application_time_format' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_time_format' => 'H:i'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('application_time_format' => 'invalid'));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('mail_sender_address' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('mail_sender_address' => 'test@localhost'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('mail_sender_address' => 'invalid'));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('mail_transport' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('mail_transport' => 'invalid'));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('default_color' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('default_color' => 'blue'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('default_color' => 'invalid'));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('board_highlight_period' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('board_highlight_period' => '10'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('board_highlight_period' => -1));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('board_public_refresh_interval' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('board_public_refresh_interval' => '0'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('board_public_refresh_interval' => -1));
        $this->assertFalse($result[0]);

        $result = $configValidator->validate(array('board_private_refresh_interval' => ''));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('board_private_refresh_interval' => '0'));
        $this->assertTrue($result[0]);

        $result = $configValidator->validate(array('board_private_refresh_interval' => -1));
        $this->assertFalse($result[0]);
    }
}
