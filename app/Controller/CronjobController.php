<?php

namespace Kanboard\Controller;

use Kanboard\Controller\BaseController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class CronjobController
 *
 * Runs cron jobs via URL
 *
 * @package Kanboard\Controller
 */
class CronjobController extends BaseController
{
    public function run()
    {
        $this->checkWebhookToken();

        $input = new ArrayInput(array(
           'command' => 'cronjob',
        ));
        $output = new NullOutput();

        $this->cli->setAutoExit(false);
        $this->cli->run($input, $output);

        $this->response->html('Cronjob executed');
    }
}
