<?php
namespace Inachis\Component\JiraIntegration\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Inachis\Component\JiraIntegration\Console\Command\Comment\CreateCommand as CreateComment;
use Inachis\Component\JiraIntegration\Console\Command\Comment\GetCommand as GetComment;
use Inachis\Component\JiraIntegration\Console\Command\Issue\CreateCommand as CreateIssue;
use Inachis\Component\JiraIntegration\Console\Command\Issue\GetCommand as GetIssue;
use Inachis\Component\JiraIntegration\Console\Command\Issue\SearchCommand as SearchIssues;
use Inachis\Component\JiraIntegration\Console\Command\Issue\GetTransitionsCommand as GetIssueTransitions;
use Inachis\Component\JiraIntegration\Console\Command\Issue\TransitionCommand as TransitionCommand;
use Inachis\Component\JiraIntegration\Console\Command\Connection\TestCommand;

/**
 * Application class for handling console access to Jira
 */
class Application extends BaseApplication
{
    const NAME = 'Jira Integration Console';
    const VERSION = '3.0.0';

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);

        $this->addCommands([
            new CreateComment(),
            new GetComment(),
            new CreateIssue(),
            new GetIssue(),
            new SearchIssues(),
            new GetIssueTransitions(),
            new TransitionCommand(),
            new TestCommand(),
        ]);
    }
}
