<?php

namespace Inachis\Component\JiraIntegration\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;
use Inachis\Component\JiraIntegration\Authentication;

/**
 * Abstract class used to contain common functionality across all commands
 */
abstract class JiraCommand extends Command
{
    /**
     * @const string The relative path to look in fr YAML config
     */
    const CONFIG_FILE = '/../../../jira.yml';
    /**
     * @var string Base64 encoded username:password pair
     */
    protected $auth;
    /**
     * @var QuestionHelper Used to provide interactive elements in console
     */
    protected $helper;
    /**
     * @var string The default project to use
     */
    protected $project;
    /**
     * @var string[] The array of custom config options
     */
    protected $config;
    /**
     * @var string[] The array of available config options for the current project
     */
    protected $availableConfig;
    /**
     * Default configuration options for console command
     */
    protected function configure()
    {
        $authProvided = false;
        if ($this->canAccessYamlConfig()) {
            $this->config = Yaml::parse(file_get_contents(__DIR__ . self::CONFIG_FILE));
            if (!empty($this->config['default']['url']) &&
                    !empty($this->config['default']['auth'])) {
                $this->setAuthentication(
                    $this->config['default']['url'],
                    $this->config['default']['auth']
                );
                $authProvided = true;
            }
            if (!empty($this->config['default']['project'])) {
                $this->defaultProject = $this->config['default']['project'];
            }
        }
        $this->addOption(
            'auth',
            null,
            $authProvided ? InputOption::VALUE_OPTIONAL :
            InputOption::VALUE_REQUIRED,
            'The base64 encoded username:password pair to use for Jira API 
                authentication'
        );
        $this->addOption(
            'url',
            null,
            $authProvided ? InputOption::VALUE_OPTIONAL :
            InputOption::VALUE_REQUIRED,
            'The URL of the Jira API to connect to'
        );
    }
    /**
     * Applies settings to the Authentication object from InputArgument if set
     * @param string $url The URL for Jira API
     * @param string $credentials The base64 encoded username:password pair
     * @throws \InvalidArgumentException
     */
    protected function connect($url, $credentials)
    {
        if (!empty($url) && !empty($credentials)) {
            $this->setAuthentication($url, $credentials);
        }
        if (empty(Authentication::getInstance()->getApiBaseUrl())) {
            throw new \InvalidArgumentException('Jira API URL must be provided');
        }
        if (empty(Authentication::getInstance()->getApiAuth())) {
            throw new \InvalidArgumentException('Credentials must be provided');
        }
    }
    /**
     * Processes the custom section of a YAML config file to prompt the user
     * for additional questions
     */
    protected function customInput()
    {
        if (empty($this->config['custom'])) {
            return;
        }
        foreach ($this->config['custom'] as $name => $argument) {
            $this->addArgument(
                $name,
                InputArgument::OPTIONAL,
                !empty($argument['help']) ? $argument['help'] : '-'
            );
        }
    }
    /**
     * Returns the result of checking if YAML config file exists and can be read
     * @return bool The result of trying to read a YAML config file
     */
    private function canAccessYamlConfig()
    {
        return file_exists(__DIR__ . self::CONFIG_FILE) &&
            is_readable(__DIR__ . self::CONFIG_FILE);
    }
    /**
     * Applies settings to the Authentication object
     * @param string $url The URL for Jira API
     * @param string $credentials The base64 encoded username:password pair
     */
    private function setAuthentication($url, $auth)
    {
        $this->auth = Authentication::getInstance($url);
        $this->auth->setApiAuth($auth);
    }
}
