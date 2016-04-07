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
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'The type of ticket being created. Default: bug')
            ->addOption('hash', null, InputOption::VALUE_OPTIONAL, 'A base64 encoded JSON object containing ticket details');
        parent::customInput();
    }
    /**
     * Configures the interactive part of the console application
     * @param InputInterface $input The console input object
     * @param OutputInterface $output The console output object
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $hash = !empty($input->getOption('hash')) ? json_decode(base64_decode($input->getOption('hash'))) : '';
        if (empty($input->getArgument('project'))) {
            $this->connect($input->getOption('url'), $input->getOption('auth'));
            $question = new ChoiceQuestion(
                'Project: ',
                Project::getInstance()->getAllProjectKeys(),
                !empty($hash->project) ? $hash->project : null
            );
            $question->setErrorMessage('Project %s is invalid');
            $input->setArgument(
                'project',
                $helper->ask($input, $output, $question)
            );
        }
        if (empty($input->getArgument('title'))) {
            $question = new Question(
                'Title: ',
                !empty($hash->subject) ? $hash->subject : null
            );
            $input->setArgument(
                'title',
                $helper->ask($input, $output, $question)
            );
        }
        if (empty($input->getArgument('description'))) {
            $question = new Question(
                'Description: ',
                !empty($hash->message) ? $hash->message : null
            );
            $input->setArgument(
                'description',
                $helper->ask($input, $output, $question)
            );
        }
        if (!empty($this->config['custom'])) {
            $this->availableConfig = Issue::getInstance()->getProjectIssueAvailableConfig(
                $input->getArgument('project')
            );
            foreach ($this->config['custom'] as $name => $argument) {
                if (empty($this->availableConfig->projects[0]->issuetypes[0]->fields->{$name}->name)) {
                    continue;
                }
                switch ($argument['type']) {
                    case 'ChoiceQuestion':
                        $allowedValues = array();
                        $customField = $this->availableConfig->projects[0]->issuetypes[0]->fields->{$name};
                        foreach ($customField->allowedValues as $allowedValue) {
                            $allowedValues[$allowedValue->id] = $allowedValue->value;
                        }
                        $question = new ChoiceQuestion(
                            $customField->name . ': ',
                            $allowedValues,
                            !empty($hash->$name) ? $hash->$name : null
                        );
                        $input->setArgument(
                            $name,
                            $helper->ask($input, $output, $question)
                        );
                        break;

                    case 'Question':
                    default:
                        $question = new Question(
                            $this->availableConfig->projects[0]->issuetypes[0]->fields->{$name}->name . ': ',
                            !empty($hash->$name) ? $hash->$name : null
                        );
                        $input->setArgument(
                            $name,
                            $helper->ask($input, $output, $question)
                        );
                }
            }
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
        $custom = $this->getCustomOptionValues($input);
        $result = Issue::getInstance()->simpleCreate(
            $input->getArgument('project'),
            $input->getArgument('title'),
            $input->getArgument('description'),
            !empty($type) ? $type : 'Bug',
            array(),
            $custom
        );
        if ($result === null || !empty($result->errors)) {
            $output->writeln(sprintf(
                '<error>Error creating ticket: %s</error>',
                implode((array) $result->errors, PHP_EOL)
            ));
        } else {
            $output->writeln(
                'Ticket created: <info>'.$result->key.'</info>' . PHP_EOL .
                'URL: <info>' . $this->auth->getApiBaseUrl() . '/browse/' . $result->key . '</info>'
            );
        }
    }
    /**
     * Returns the array of custom field values once processed dependent upon
     * their question type.
     * @param InputInterface $input The console input object
     * @return string[] The array of customfield values
     */
    private function getCustomOptionValues($input)
    {
        if (empty($this->config['custom'])) {
            return array();
        }

        $custom = array();
        foreach ($this->config['custom'] as $name => $argument) {
            switch ($argument['type']) {
                case 'ChoiceQuestion':
                    $questionAnswer = $input->getArgument($name);
                    $allowedValues = $this->availableConfig->projects[0]->issuetypes[0]->fields->{$name}->allowedValues;
                    foreach ($allowedValues as $allowedValue) {
                        if ($allowedValue->value == $questionAnswer) {
                            $questionAnswer = (string) $allowedValue->id;
                            break;
                        }
                    }
                    $custom[$name] = array((object) array(
                        'id' => $questionAnswer
                    ));
                    break;

                case 'Question':
                default:
                    $custom[$name] = $input->getArgument($name);
            }
        }
        return $custom;
    }
}
