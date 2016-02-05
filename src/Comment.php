<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

/**
 * Object for interacting with comment part of the 
 * Issue resources from the Jira API
 */
class Comment extends JiraConnection {
	/**
	 * @var Authentication Reference to instance of self
	 */
	private static $instance;
	/**
	 * Returns a singleton instance of this class
	 * @return Comment The singleton instance
	 */
	public static function getInstance()
	{
		if (null === static::$instance) {
			static::$instance = new static();
		}
		return static::$instance;
	}
    /**
     * Adds a comment to the specified ticket
     * @param string $issue_key The ticket to be updated
     * @param string $text The markdown supported comment to add
     * @param string[] Optional array of visibility details for comment
     * @return stdClass The result of adding the comment
     */
    public function create($issue_key, $text, $visibility = null)
    {
        $data - array('body' => $text);
        if (!empty($visibility)) {
            $data['visibility'] = $visibility;
        }
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/comment', 
            $data,
            'POST'
        );
    }
    /**
     * Updated the specified comment
     * @param string $issue_key The ticket to be updated
     * @param string $comment_id The ID of the comment to update
     * @param string $text The markdown supported comment to add
     * @param string[] Optional array of visibility details for comment
     * @return stdClass The result of updating the comment
     */
    public function update(
        $issue_key,
        $comment_id,
        $text,
        $visibility = null,
        $expand = false
    ) {
        $data - array('body' => $text);
        if (!empty($visibility)) {
            $data['visibility'] = $visibility;
        }
        if ($expand) {
            $data['expand'] = 'true';
        }
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            $data,
            'PUT'
        );
    }
    /**
     * Deletes the specified comment from the issue
     * @param string $issue_key The ticket to be updated
     * @param string $comment_id The ID of the comment to update
     * @return stdClass The result of deleting the comment from the issue
     */
    public function delete($issue_key, $comment_id)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            array(),
            'DELETE'
        );
    }
    /**
     * Returns a specific comment for an issue
     * @param string $issue_key The issue to get comments for
     * @param string $comment_id The ID of the comment to retrieve
     * @param bool $expand Flag indicating if comment should be HTML
     * @return stdClass The requested comment
     */
    public function get($issue_key, $comment_id, $expand = false)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            $expand ? array('expand' => 'true') : array(),
            'GET'
        );
    }
    /**
     * Returns an array of all commments for the specified issue
     * @param string $issue_key The issue to get comments for
     * @param bool $expand Flag indicating if comment should be HTML
     * @return stdClass The comments for the given issue
     */
    public function getAll($issue_key, $expand = false)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/comment',
            $expand ? array('expand' => 'true') : array(),
            'GET'
        );
    }
}
