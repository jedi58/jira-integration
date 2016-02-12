<?php
namespace Inachis\Component\JiraIntegration\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Inachis\Component\JiraIntegration\Console\Command\Comment\CreateCommand as CreateComment;
use Inachis\Component\JiraIntegration\Console\Command\Comment\GetCommand as GetComment;
use Inachis\Component\JiraIntegration\Console\Command\Issue\CreateCommand as CreateIssue;
use Inachis\Component\JiraIntegration\Console\Command\Issue\GetCommand as GetIssue;

/**
 * Application class for handling console access to Jira
 */
class Application extends BaseApplication
{
    const NAME = 'Jira Integration Console';
    const VERSION = '1.2.0';

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);

        $this->addCommands(array(
            new CreateComment(),
            new GetComment(),
            new CreateIssue(),
            new GetIssue()
        ));
    }
}
