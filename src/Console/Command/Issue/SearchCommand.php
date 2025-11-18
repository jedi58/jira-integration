<?php

namespace Inachis\Component\JiraIntegration\Console\Command\Issue;

use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Inachis\Component\JiraIntegration\Issue;
use Inachis\Component\JiraIntegration\Console\Command\JiraCommand;

/**
 * Defines the issue:search command for the console application
 */
class SearchCommand extends JiraCommand
{
    /**
     * Configuration for the console command
     */
    protected function configure() : void
    {
        parent::configure();
        $this
            ->setName('issue:search')
            ->setAliases(['i:s', 'is'])
            ->setDescription('Fetches a list of issue keys matching JQL')
            ->addArgument(
                'jql',
                InputArgument::REQUIRED,
                'JQL used for searching'
            );
    }
    /**
     * Configures the interactive part of the console application
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function interact(InputInterface $input, OutputInterface $output) : void
    {
        if (empty($input->getArgument('jql'))) {
            $this->connect($input->getOption('url'), $input->getOption('username'), $input->getOption('token'));
            $question = new Question('JQL: ');
            $input->setArgument(
                'jql',
                $this->getHelper('question')->ask($input, $output, $question)
            );
        }
    }
    /**
     * Retrieves and prints a specified Jira ticket
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->connect($input->getOption('url'), $input->getOption('username'), $input->getOption('token'));
        $result = Issue::getInstance()->search(
            [
                'fields' => [
                    'id',
                    'key',
                    'summary',
                ],
                'jql' => $input->getArgument('jql'),
            ]
        );
        if ($result === null || !empty($result->errors)) {
            $output->writeln(sprintf(
                '<error>Error with jql statement: %s</error>',
                implode(PHP_EOL, (array) $result->errors)
            ));
        } else {
            $this->prettyPrintTicket($result, $output);
        }
        return Command::SUCCESS;
    }
    /**
     * Displays a summary of the retrieved ticket with formating
     * @param stdClass $ticket The returned ticket
     * @param OutputInterface $output The console output object
     */
    private function prettyPrintTicket($result, OutputInterface $output) : void
    {
        if (empty($result->issues)) {
            $output->writeln('<info>No issues found.</info>');
        } else {
            foreach ($result->issues as $issue) {
                $output->writeln(sprintf('%s: %s', $issue->key, trim($issue->fields->summary)));
            }
        }
    }
}
