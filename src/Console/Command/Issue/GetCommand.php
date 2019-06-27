<?php

namespace Inachis\Component\JiraIntegration\Console\Command\Issue;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Inachis\Component\JiraIntegration\Project;
use Inachis\Component\JiraIntegration\Issue;
use Inachis\Component\JiraIntegration\Console\Command\JiraCommand;

/**
 * Defines the issue:get command for the console application
 */
class GetCommand extends JiraCommand
{
    /**
     * Configuration for the console command
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('issue:get')
            ->setDescription('Fetches details of a specific Jira issue specified by it\'s key. e.g. DEMO-1234')
            ->addArgument(
                'issue-key',
                InputArgument::OPTIONAL,
                'The issue to update'
            );
    }
    /**
     * Configures the interactive part of the console application
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (empty($input->getArgument('issue-key'))) {
            $this->connect($input->getOption('url'), $input->getOption('username'), $input->getOption('token'));
            $question = new Question('Issue key: ');
            $question->setAutocompleterValues(
                Project::getInstance()->getAllProjectKeys()
            );
            $input->setArgument(
                'issue-key',
                $this->getHelper('question')->ask($input, $output, $question)
            );
        }
    }
    /**
     * Retrieves and prints a specified Jira ticket
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->connect($input->getOption('url'), $input->getOption('username'), $input->getOption('token'));
        $result = Issue::getInstance()->get(
            $input->getArgument('issue-key')
        );
        if ($result === null || !empty($result->errors)) {
            $output->writeln(sprintf(
                '<error>Error retrieving ticket `%s`: %s</error>',
                $input->getArgument('issue-key'),
                implode((array) $result->errors, PHP_EOL)
            ));
        } else {
            $this->prettyPrintTicket($result, $output);
        }
    }
    /**
     * Displays a summary of the retrieved ticket with formating
     * @param StdClass $ticket The returned ticket
     * @param OutputInterface $output The console output object
     */
    private function prettyPrintTicket($ticket, OutputInterface $output)
    {
        $output->writeln(sprintf('Ticket: <info>%s</info>', $ticket->key));
        $output->writeln(sprintf(
            'Priority: <info>%s</info>',
            $ticket->fields->priority->name
        ));
        $output->writeln(sprintf(
            'Status: <info>%s</info>',
            $ticket->fields->status->name
        ));
        $output->writeln(sprintf(
            'Type: <info>%s</info>',
            $ticket->fields->issuetype->name
        ));
        $output->writeln('-----' . PHP_EOL . $ticket->fields->description);
    }
}
