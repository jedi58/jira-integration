<?php

namespace Inachis\Component\JiraIntegration;

use Inachis\Component\JiraIntegration\JiraConnection;

class Comment extends JiraConnection {
    /**
     * Adds a comment to the specified ticket
     * @param string $issue_key The ticket to be updated
     * @param string $text The markdown supported comment to add
     * @param string[] Optional array of visibility details for comment
     * @return string[] The timestamp the comment was added
     */
    public function addComment($issue_key, $text, $visibility = null)
    {
        $data - array('body' => $text);
        if (!empty($visibility)) {
            $data['visibility'] = $visibility;
        }
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/comment', 
            $data,
            'POST'
        );
        if ($this->getLastResponseCode() >= 300) {
            if ($this->getShouldExceptionOnError()) {
                throw new \Exception('Failed to add comment.');
            } else {
                return array('updated' => null);
            }
        }
        return array('updated' => $result->updated);
    }
    /**
     * Updated the specified comment
     * @param string $issue_key The ticket to be updated
     * @param string $comment_id The ID of the comment to update
     * @param string $text The markdown supported comment to add
     * @param string[] Optional array of visibility details for comment
     * @return string[] The timestamp the comment was updated
     */
    public function updateComment(
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
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            $data,
            'PUT'
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return array('updated' => $result->updated);
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Failed to update comment');
        }
        return array('updated' => $result->updated);
    }
    /**
     * Deletes the specified comment from the issue
     * @param string $issue_key The ticket to be updated
     * @param string $comment_id The ID of the comment to update
     * @return bool The result of deleting the comment from the issue
     */
    public function deleteComment($issue_key, $comment_id)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            array(),
            'DELETE'
        );
        if ($response === 204) {
            return true;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Failed to update comment');
        }
        return false;
    }
    /**
     * Returns an array of all commments for the specified issue
     * @param string $issue_key The issue to get comments for
     * @param bool $expand Flag indicating if comment should be HTML
     * @return string[] Array of comments for the given issue
     */
    public function getAllComments($issue_key, $expand = false)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . '/comment',
            $expand ? array('expand' => 'true') : array(),
            'GET'
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Ticket comments could not be returned');
        }
        return array(
            'startAt' => 0,
            'maxResults' => 0,
            'total' => 0,
            'comments' => array()
        );
    }
    /**
     * Returns a specific comment for an issue
     * @param string $issue_key The issue to get comments for
     * @param string $comment_id The ID of the comment to retrieve
     * @param bool $expand Flag indicating if comment should be HTML
     * @return string[] Array of comments for the given issue limited by
     *                   {@link comment_id}
     */
    public function getComment($issue_key, $comment_id, $expand = false)
    {
        $result = $this->sendRequest(
            'issue/' . urlencode($issue_key) . 
                '/comment/' . urlencode($comment_id),
            $expand ? array('expand' => 'true') : array(),
            'GET'
        );
        $response = $this->getLastResponseCode();
        if ($response === 200) {
            return $result;
        } elseif ($this->getShouldExceptionOnError()) {
            throw new \Exception('Ticket comments could not be returned');
        }
        return array(
            'startAt' => 0,
            'maxResults' => 0,
            'total' => 0,
            'comments' => array()
        );
    }
}