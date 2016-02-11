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
 * Defines the issue:create command for the console application
 */
class CreateCommand extends JiraCommand
{
    /**
     * Configuration for the console command
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('issue:create')
            ->setDescription('Creates a new Jira ticket and returns the unique key')
            ->addArgument('project', InputArgument::OPTIONAL, 'The project to add the ticket to')
            ->addArgument('title', InputArgument::OPTIONAL, 'The title of the ticket being created')
            ->addArgument('description', InputArgument::OPTIONAL, 'The description for the ticket being created')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'The type of ticket being created. Default: bug');
    }
    /**
     * Configures the interactive part of the console application
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        if (empty($input->getArgument('project'))) {
            $this->connect($input->getOption('url'), $input->getOption('auth'));
            $question = new ChoiceQuestion(
                'Project: ',
                Project::getInstance()->getAllProjectKeys()
            );
            $question->setErrorMessage('Project %s is invalid');
            $input->setArgument(
                'project',
                $helper->ask($input, $output, $question)
            );
        }
        if (empty($input->getArgument('title'))) {
            $question = new Question('Title: ');
            $input->setArgument(
                'title',
                $helper->ask($input, $output, $question)
            );
        }
        if (empty($input->getArgument('description'))) {
            $question = new Question('Description: ');
            $input->setArgument(
                'description',
                $helper->ask($input, $output, $question)
            );
        }
    }
    /**
     * Creates the Jira ticket and outputs the issue-key
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getOption('type');
        $this->connect($input->getOption('url'), $input->getOption('auth'));
        $result = Issue::getInstance()->simpleCreate(
            $input->getArgument('project'),
            $input->getArgument('title'),
            $input->getArgument('description'),
            !empty($type) ? $type : 'Bug'
        );
        if ($result === null || !empty($result->errors)) {
            $output->writeln(sprintf(
                '<error>Error creating ticket: %s</error>',
                implode((array) $result->errors, PHP_EOL)
            ));
        } else {
            $output->writeln('Ticket created: <info>'.$result->key.'</info>');
        }
    }
}
