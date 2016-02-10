<?php
namespace Inachis\Component\JiraIntegration\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Inachis\Component\JiraIntegration\Console\Command\Issue\CreateCommand;
use Inachis\Component\JiraIntegration\Console\Command\Issue\GetCommand;

class Application extends BaseApplication
{
    const NAME = 'Jira Integration Console';
    const VERSION = '1.2.0';

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);

        $this->addCommands(array(
            new Command\Issue\CreateCommand(),
            new Command\Issue\GetCommand()
        ));
    }
}
