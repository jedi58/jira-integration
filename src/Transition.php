<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;
use Inachis\Component\JiraIntegration\Issue;
use Inachis\Component\JiraIntegration\Transformer\AdfTransformer;

/**
 * Object for interacting with Issue transitions in Jira
 */
class Transition extends JiraConnection
{
    /**
     * @var Transition Reference to instance of self
     */
    private static $instance;
    /**
     * Returns a singleton instance of this class
     * @return Transition The singleton instance
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Returns all available transitions for applying to a given issue
     * @param string $issue The issue to get available transitions for
     * @return StdClass|string The object containing transition data
     * @throws \Exception
     */
    public function getAll(string $issue)
    {
        return $this->sendRequest('issue/' . urlencode($issue) . '/transitions');
    }

    /**
     * Applies a transition by the name given to that transition by first looking at the API for available
     * transitions and then using the name provided to determine the ID of the transition to apply before applying it.
     * @param string $issue The issue to transition
     * @param string $transitionName The name of the transition to apply
     * @param string|null $comment A comment to add to the issue upon transitioning
     * @throws \Exception
     */
    public function applyTransitionByName(string $issue, string $transitionName, ?string $comment = '')
    {
        $transitions = $this->getAll($issue);
        if (!empty($transitions->transitions)) {
            foreach ($transitions->transitions as $transition) {
                if ($transition->name == $transitionName) {
                    $this->applyTransition($issue, (string) intval($transition->id), $comment);
                    break;
                }
            }
        }
    }

    /**
     * Applies the specified transition to the given issue
     * @param string $issue The key of the issue to apply the transition to
     * @param int $transitionId The ID of the transition to apply
     * @param string|null $comment A comment to  add to the issue upon transitioning
     * @throws \Exception
     */
    public function applyTransition(string $issue, string $transitionId, ?string $comment = '')
    {
        $transition = [
            'transition' => [
                'id' => (string) $transitionId,
            ]
        ];
        if (!empty($comment)) {
            $transition['update']['comment'][] = [ 'add'=> [ 'body' => AdfTransformer::getInstance()->transformToAdf($comment) ] ];
        }
        $this->sendRequest('issue/' . urlencode($issue) . '/transitions', $transition, 'POST');
    }
}
