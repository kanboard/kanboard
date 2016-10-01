<?php

class Base extends PHPUnit_Extensions_Selenium2TestCase
{

    public function setUp()
    {
        $this->setHost(SELENIUM_HOST);
        $this->setPort((integer) SELENIUM_PORT);
        $this->setBrowserUrl(KANBOARD_APP_URL);
        $this->setBrowser(DEFAULT_BROWSER);
    }

    public function tearDown()
    {
        $this->stop();
    }
}
