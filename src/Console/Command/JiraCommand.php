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
    protected function configure() : void
    {
        $authProvided = false;
        if ($this->canAccessYamlConfig()) {
            $this->config = Yaml::parse(file_get_contents(__DIR__ . self::CONFIG_FILE));
            if (!empty($this->config['default']['url']) &&
                    !empty($this->config['default']['username']) &&
                    !empty($this->config['default']['token'])) {
                $this->setAuthentication(
                    $this->config['default']['url'],
                    $this->config['default']['username'],
                    $this->config['default']['token']
                );
                $authProvided = true;
            }
            if (!empty($this->config['default']['project'])) {
                $this->defaultProject = $this->config['default']['project'];
            }
        }
        $this->addOption(
            'username',
            null,
            $authProvided ? InputOption::VALUE_OPTIONAL :
            InputOption::VALUE_REQUIRED,
            'The colon separated username to use for Jira API
                authentication'
        );
        $this->addOption(
            'token',
            null,
            $authProvided ? InputOption::VALUE_OPTIONAL :
                InputOption::VALUE_REQUIRED,
            'The colon separated username to use for Jira API
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
     * @param string $username The username to connect with
     * @param string $token The token to connect with
     * @throws \InvalidArgumentException
     */
    protected function connect($url, $username, $token) : void
    {
        if (!empty($url) && !empty($credentials)) {
            $this->setAuthentication($url, $username, $token);
        }
        if (empty(Authentication::getInstance()->getApiBaseUrl())) {
            throw new \InvalidArgumentException('Jira API URL must be provided');
        }
        if (empty(Authentication::getInstance()->getUsername()) || empty(Authentication::getInstance()->getToken())) {
            throw new \InvalidArgumentException('Credentials must be provided');
        }
    }
    /**
     * Processes the custom section of a YAML config file to prompt the user
     * for additional questions
     */
    protected function customInput() : void
    {
        if (!empty($this->config['custom'])) {
            foreach ($this->config['custom'] as $name => $argument) {
                $this->addArgument(
                    $name,
                    InputArgument::OPTIONAL,
                    !empty($argument['help']) ? $argument['help'] : '-'
                );
            }
        }
    }
    /**
     * Returns the result of checking if YAML config file exists and can be read
     * @return bool The result of trying to read a YAML config file
     */
    private function canAccessYamlConfig() : bool
    {
        return file_exists(__DIR__ . self::CONFIG_FILE) &&
            is_readable(__DIR__ . self::CONFIG_FILE);
    }
    /**
     * Applies settings to the Authentication object
     * @param string $url The URL for Jira API
     * @param string $credentials The base64 encoded username:password pair
     */
    private function setAuthentication($url, $username, $token) : void
    {
        $this->auth = Authentication::getInstance($url);
        $this->auth->setUsername($username);
        $this->auth->setToken($token);
    }
}
