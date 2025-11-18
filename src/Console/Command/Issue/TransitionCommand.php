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
 * Defines the issue:transition command for the console application
 */
class TransitionCommand extends JiraCommand
{
    /**
     * Configuration for the console command
     */
    protected function configure() : void
    {
        parent::configure();
        $this
            ->setName('issue:transition')
            ->setDescription('Transition an issue by it\'s key. (e.g. DEMO-1234) using transition id or name')
            ->addArgument(
                'issue-key',
                InputArgument::REQUIRED,
                'The issue to get available transitions for'
            )
            ->addArgument(
                'transition',
                InputArgument::REQUIRED,
                'The transition (by id or name) to apply'
            )
            ->addArgument(
                'comment',
                InputArgument::OPTIONAL,
                'A comment to apply with the transition'
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
        if (empty($input->getArgument('transition'))) {
            $question = new ChoiceQuestion(
                'Transition: ',
                $this->getAvailableTransitions($input->getArgument('issue-key'))
            );
            $question->setErrorMessage('%s is an invalid transition');
            $input->setArgument(
                'transition',
                $this->getHelper('question')->ask($input, $output, $question)
            );
        }
    }
    /**
     * Applies the specified transition to the specified case
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->connect($input->getOption('url'), $input->getOption('username'), $input->getOption('token'));
        $comment = $input->getArgument('comment');
        if (intval($input->getArgument('transition')) == $input->getArgument('transition')) {
            $result = Transition::getInstance()->applyTransition($input->getArgument('issue-key'), (string) intval($input->getArgument('transition')), $comment);
        } else {
            $result = Transition::getInstance()->applyTransitionByName($input->getArgument('issue-key'), $input->getArgument('transition'), $comment);
        }
        $output->writeln(sprintf(
            'Applied transition %s to issue `%s`',
            (string) $input->getArgument('transition'),
            $input->getArgument('issue-key')
        ));
        return Command::SUCCESS;
    }
    /**
     * Returns a list of available transitions for a given issue where the keys are the ids, and values are the names
     * @param string $issue The issue to get transitions for
     * @return array The array of available transitions
     * @throws \Exception
     */
    private function getAvailableTransitions(string $issue) : array
    {
        $transitions = Transition::getInstance()->getAll($issue);
        $available_transitions = [];
        foreach ($transitions->transitions as $transition) {
            $available_transitions[$transition->id] = $transition->name;
        }
        return $available_transitions;
    }
}
