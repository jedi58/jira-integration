<?php

namespace Inachis\Component\JiraIntegration\Console\Command\Comment;

use Symfony\Component\Console\Command\Command;
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
 * Defines the comment:get command for the console application
 */
class GetCommand extends JiraCommand
{
    /**
     * Configuration for the console command
     */
    protected function configure() : void
    {
        parent::configure();
        $this
            ->setName('comment:get')
            ->setDescription('Fetches details all comemnts for a specific
                Jira issue identified by it\'s key. e.g. DEMO-1234')
            ->addArgument(
                'issue-key',
                InputArgument::OPTIONAL,
                'The issue to get comments for'
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
            $this->connect($input->getOption('url'), $input->getOption('auth'));
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
     * Retrieves and prints a specified Jira ticket's comemnts
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->connect($input->getOption('url'), $input->getOption('auth'));
        $result = Comment::getInstance()->getAll(
            $input->getArgument('issue-key')
        );
        if ($result === null || !empty($result->errors)) {
            $output->writeln(sprintf(
                '<error>Error retrieving comments for ticket `%s`: %s</error>',
                $input->getArgument('issue-key'),
                implode(PHP_EOL, (array) $result->errors)
            ));
        } else {
            $this->prettyPrintComments($result, $output);
        }

        return Command::SUCCESS;
    }
    /**
     * Displays a summary of the retrieved ticket's comments with formating
     * @param StdClass $comments The returned ticket
     * @param OutputInterface $output The console output object
     */
    private function prettyPrintComments($comments, OutputInterface $output) : void
    {
        foreach ($comments->comments as $key => $comment) {
            $output->writeln(sprintf(
                '<info>[%d] Comment from %s at %s</info>',
                (int) $key,
                $comment->author->displayName,
                $comment->updated
            ));
            $output->writeln($comment->body . PHP_EOL);
        }
    }
}
