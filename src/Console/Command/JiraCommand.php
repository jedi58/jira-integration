<?php

namespace Inachis\Component\JiraIntegration\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Inachis\Component\JiraIntegration\Authentication;

/**
 *
 */
abstract class JiraCommand extends Command
{
    protected $auth;

    protected $helper;

    protected function configure()
    {
        $this->addOption(
            'auth',
            null,
            InputOption::VALUE_REQUIRED,
            'The base64 encoded username:password pair to use for Jira API 
                authentication'
        );
        $this->addOption(
            'url',
            null,
            InputOption::VALUE_REQUIRED,
            'The URL of the Jira API to connect to'
        );
    }

    protected function connect($url, $credentials)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Jira API URL must be provided');
        }
        if (empty($credentials)) {
            throw new \InvalidArgumentException('Credentials must be provided');
        }
        $this->auth = Authentication::getInstance($url);
        $this->auth->setApiAuth($credentials);
    }
}
