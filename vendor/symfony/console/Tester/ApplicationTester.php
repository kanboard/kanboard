<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tester;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Eases the testing of console applications.
 *
 * When testing an application, don't forget to disable the auto exit flag:
 *
 *     $application = new Application();
 *     $application->setAutoExit(false);
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ApplicationTester
{
    use TesterTrait;

    private Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Executes the application.
     *
     * Available options:
     *
     *  * interactive:               Sets the input interactive flag
     *  * decorated:                 Sets the output decorated flag
     *  * verbosity:                 Sets the output verbosity flag
     *  * capture_stderr_separately: Make output of stdOut and stdErr separately available
     *
     * @return int The command exit code
     */
    public function run(array $input, array $options = []): int
    {
        $prevShellVerbosity = [getenv('SHELL_VERBOSITY'), $_ENV['SHELL_VERBOSITY'] ?? false, $_SERVER['SHELL_VERBOSITY'] ?? false];

        try {
            $this->input = new ArrayInput($input);
            if (isset($options['interactive'])) {
                $this->input->setInteractive($options['interactive']);
            }

            if ($this->inputs) {
                $this->input->setStream(self::createStream($this->inputs));
            }

            $this->initOutput($options);

            // Temporarily clear SHELL_VERBOSITY to prevent Application::configureIO
            // from overriding the interactive and verbosity settings set above
            if (\function_exists('putenv')) {
                @putenv('SHELL_VERBOSITY');
            }
            unset($_ENV['SHELL_VERBOSITY'], $_SERVER['SHELL_VERBOSITY']);

            return $this->statusCode = $this->application->run($this->input, $this->output);
        } finally {
            // SHELL_VERBOSITY is set by Application::configureIO so we need to unset/reset it
            // to its previous value to avoid one test's verbosity to spread to the following tests
            if (false === $prevShellVerbosity[0]) {
                if (\function_exists('putenv')) {
                    @putenv('SHELL_VERBOSITY');
                }
            } else {
                if (\function_exists('putenv')) {
                    @putenv('SHELL_VERBOSITY='.$prevShellVerbosity[0]);
                }
            }
            if (false === $prevShellVerbosity[1]) {
                unset($_ENV['SHELL_VERBOSITY']);
            } else {
                $_ENV['SHELL_VERBOSITY'] = $prevShellVerbosity[1];
            }
            if (false === $prevShellVerbosity[2]) {
                unset($_SERVER['SHELL_VERBOSITY']);
            } else {
                $_SERVER['SHELL_VERBOSITY'] = $prevShellVerbosity[2];
            }
        }
    }
}
