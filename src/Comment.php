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
     * @param string $issueKey The ticket to be updated
     * @param string $text The markdown supported comment to add
     * @param string[] Optional array of visibility details for comment
     * @return stdClass The result of adding the comment
     */
    public function create($issueKey, $text, $visibility = null)
    {
        $data = array('body' => $text);
        if (!empty($visibility)) {
            $data['visibility'] = $visibility;
        }
        return $this->sendRequest(
            'issue/' . urlencode($issueKey) . '/comment', 
            $data,
            'POST'
        );
    }
    /**
     * Updated the specified comment
     * @param string $issueKey The ticket to be updated
     * @param string $commentId The ID of the comment to update
     * @param string $text The markdown supported comment to add
     * @param string[] Optional array of visibility details for comment
     * @return stdClass The result of updating the comment
     */
    public function update(
        $issueKey,
        $commentId,
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
            'issue/' . urlencode($issueKey) . 
                '/comment/' . urlencode($commentId),
            $data,
            'PUT'
        );
    }
    /**
     * Deletes the specified comment from the issue
     * @param string $issueKey The ticket to be updated
     * @param string $commentId The ID of the comment to update
     * @return stdClass The result of deleting the comment from the issue
     */
    public function delete($issueKey, $commentId)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issueKey) . 
                '/comment/' . urlencode($commentId),
            array(),
            'DELETE'
        );
    }
    /**
     * Returns a specific comment for an issue
     * @param string $issueKey The issue to get comments for
     * @param string $commentId The ID of the comment to retrieve
     * @return stdClass The requested comment
     */
    public function get($issueKey, $commentId)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issueKey) . 
                '/comment/' . urlencode($commentId)
        );
    }
    /**
     * Returns a specific comment for an issue
     * @param string $issueKey The issue to get comments for
     * @param string $commentId The ID of the comment to retrieve
     * @return stdClass The requested comment
     */
    public function getAsHtml($issueKey, $commentId)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issueKey) . 
                '/comment/' . urlencode($commentId),
            array('expand' => 'true')
        );
    }
    /**
     * Returns an array of all commments for the specified issue
     * @param string $issueKey The issue to get comments for
     * @return stdClass The comments for the given issue
     */
    public function getAll($issueKey, $expand = false)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issueKey) . '/comment',
        );
    }
    /**
     * Returns an array of all commments for the specified issue
     * @param string $issueKey The issue to get comments for
     * @return stdClass The comments for the given issue
     */
    public function getAllAsHtml($issueKey)
    {
        return $this->sendRequest(
            'issue/' . urlencode($issueKey) . '/comment',
            array('expand' => 'true')
        );
    }
}
