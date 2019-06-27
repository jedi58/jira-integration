<?php

namespace Inachis\Component\JiraIntegration\Console\Command\Connection;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Inachis\Component\JiraIntegration\Status;
use Inachis\Component\JiraIntegration\Console\Command\JiraCommand;

/**
 * Defines the connection:test command for the console application
 */
class TestCommand extends JiraCommand
{
    /**
     * Configuration for the console command
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('connection:test')
            ->setDescription('Tests the connection to the Jira API');
    }
    /**
     * Configures the interactive part of the console application
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
    }
    /**
     * Performs a connection test
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->connect($input->getOption('url'), $input->getOption('username'), $input->getOption('token'));
        $result = Status::getInstance()->get();
        $responseCode = Status::getInstance()->getLastResponseCode();
        $responseType = $responseCode >= 400 ? 'error' : 'info';
        $output->writeln(sprintf(
            '<%s>Response code:</%s> %s - %s',
            $responseType,
            $responseType,
            $responseCode,
            Status::getInstance()->getHTTPStatusCodeAsText($responseCode)
        ));
        $output->writeln(sprintf('Output: %s', substr(json_encode(Status::getInstance()->getResult()), 0, 500) . '…'));
    }
}
