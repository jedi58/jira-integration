<?php

namespace Inachis\Component\JiraIntegration\Command\Comment;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Inachis\Component\JiraIntegration\Command\JiraCommand;
use Inachis\Component\JiraIntegration\Comment;

class CreateCommand extends JiraCommand
{
    protected function configure()
    {
        $this->setName('comment:create')
            ->setDescription('Creates a Jira ticket')
            ->setDefinition($this->getDefaultParameters());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $auth = $input->getOption('auth');
        if (empty($auth)) {
            throw new \InvalidArgumentException('Credentials must be provided');
        }
    }
}
