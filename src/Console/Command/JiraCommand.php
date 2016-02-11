<?php

namespace Inachis\Component\JiraIntegration\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;
use Inachis\Component\JiraIntegration\Authentication;

/**
 *
 */
abstract class JiraCommand extends Command
{
    const CONFIG_FILE = '/../../../jira.yml';
    protected $auth;

    protected $helper;

    protected function configure()
    {
        $authProvided = false;
        // get config from YAML if available
        if ($this->canAccessYamlConfig()) {
            $config = Yaml::parse(file_get_contents(__DIR__ . self::CONFIG_FILE));
            $this->setAuthentication(
                $config['default']['url'],
                $config['default']['auth']
            );
            $authProvided = true;
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

    private function canAccessYamlConfig()
    {
        return file_exists(__DIR__ . self::CONFIG_FILE) &&
            is_readable(__DIR__ . self::CONFIG_FILE);
    }

    private function setAuthentication($url, $auth)
    {
        $this->auth = Authentication::getInstance($url);
        $this->auth->setApiAuth($auth);
    }
}
