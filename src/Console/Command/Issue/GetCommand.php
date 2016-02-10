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
 *
 */
class GetCommand extends JiraCommand
{
	/**
	 *
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

	protected function interact(InputInterface $input, OutputInterface $output)
	{
		if (empty($input->getArgument('issue-key'))) {
			$this->connect($input->getOption('url'), $input->getOption('auth'));
			$projects = Project::getInstance()->getAllProjectKeys();
			$helper = $this->getHelper('question');
			$question = new Question('Issue key: ');
			$question->setAutocompleterValues($projects);
			$issue = $helper->ask($input, $output, $question);
			$input->setArgument('issue-key', $issue);
		}
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->connect($input->getOption('url'), $input->getOption('auth'));
		$result = Issue::getInstance()->get(
			$input->getArgument('issue-key')
		);
		if (!empty($result->errors)) {
			$output->writeln(sprintf('<error>Error retrieving ticket: %s</error>',
				implode((array) $result->errors, PHP_EOL)));
		} else {
			$output->writeln('Ticket: ' . print_r($result, true));
		}
	}
}
