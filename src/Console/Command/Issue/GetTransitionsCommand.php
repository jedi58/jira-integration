<?php

namespace Inachis\Component\JiraIntegration\Console\Command\Issue;

use Inachis\Component\JiraIntegration\Transformer\AdfTransformer;
use Inachis\Component\JiraIntegration\Transition;
use Symfony\Component\Console\Command\Command;
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
 * Defines the issue:get-transitions command for the console application
 */
class GetTransitionsCommand extends JiraCommand
{
    /**
     * Configuration for the console command
     */
    protected function configure() : void
    {
        parent::configure();
        $this
            ->setName('issue:get-transitions')
            ->setDescription('Fetches transitions available for an issue by it\'s key. e.g. DEMO-1234')
            ->addArgument(
                'issue-key',
                InputArgument::REQUIRED,
                'The issue to get available transitions for'
            );
    }
    /**
     * Configures the interactive part of the console application
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function interact(InputInterface $input, OutputInterface $output) : void
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
     * Retrieves and prints available transitions for a specified Jira ticket
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->connect($input->getOption('url'), $input->getOption('username'), $input->getOption('token'));
        $result = Transition::getInstance()->getAll(
            $input->getArgument('issue-key')
        );
        if ($result === null || !empty($result->errors)) {
            $output->writeln(sprintf(
                '<error>Error retrieving ticket `%s`: %s</error>',
                $input->getArgument('issue-key'),
                implode(PHP_EOL, (array) $result->errors)
            ));
        } else {
            $this->prettyPrintTransitions($result, $output);
        }
        return Command::SUCCESS;
    }
    /**
     * Displays a summary of available transitions for a ticket
     * @param StdClass $ticket The transitions available
     * @param OutputInterface $output The console output object
     */
    private function prettyPrintTransitions($transitions, OutputInterface $output) : void
    {
        foreach ($transitions->transitions as $transition) {
            $output->writeln(sprintf('%d - %s => %s', (int) $transition->id, $transition->name, $transition->to->name));
        }
    }
}
