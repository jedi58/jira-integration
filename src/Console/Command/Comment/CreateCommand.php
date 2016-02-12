<?php

namespace Inachis\Component\JiraIntegration\Console\Command\Comment;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Inachis\Component\JiraIntegration\Project;
use Inachis\Component\JiraIntegration\Comment;
use Inachis\Component\JiraIntegration\Console\Command\JiraCommand;

/**
 * Defines the comment:create command for the console application
 */
class CreateCommand extends JiraCommand
{
    /**
     * Configuration for the console command
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('comment:create')
            ->setDescription('Adds a comment to a specified Jira ticket')
            ->addArgument('issue', InputArgument::OPTIONAL, 'The issue to add a comment to')
            ->addOption('message', 'm', InputOption::VALUE_OPTIONAL, 'The comment to add');
    }
    /**
     * Configures the interactive part of the console application
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        if (empty($input->getArgument('issue'))) {
            $question = new Question('Issue Key: ');
            $input->setArgument(
                'issue',
                $helper->ask($input, $output, $question)
            );
        }
        if (empty($input->getOption('message'))) {
            $question = new Question('Message: ');
            $input->setOption(
                'message',
                $helper->ask($input, $output, $question)
            );
        }
    }
    /**
     * Adds the comment and outputs the key of the new comment
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->connect($input->getOption('url'), $input->getOption('auth'));
        $result = Comment::getInstance()->create(
            $input->getArgument('issue'),
            $input->getOption('message')
        );
        if ($result === null || !empty($result->errors)) {
            $output->writeln(sprintf(
                '<error>Error creating comment: %s</error>',
                implode((array) $result->errors, PHP_EOL)
            ));
        } else {
            $output->writeln(
                'Comment created: <info>' . $result->id . '</info>'
            );
        }
    }
}
