<?php

namespace Integration;

use HTML_To_Markdown;

/**
 * Mailgun Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class MailgunWebhook extends Base
{
    /**
     * Parse incoming email
     *
     * @access public
     * @param  array   $payload   Incoming email
     * @return boolean
     */
    public function parsePayload(array $payload)
    {
        if (empty($payload['sender']) || empty($payload['subject']) || empty($payload['recipient']) || empty($payload['stripped-text'])) {
            return false;
        }

        // The user must exists in Kanboard
        $user = $this->user->getByEmail($payload['sender']);

        if (empty($user)) {
            $this->container['logger']->debug('MailgunWebhook: ignored => user not found');
            return false;
        }

        // The project must have a short name
        $project = $this->project->getByIdentifier($this->getMailboxHash($payload['recipient']));

        if (empty($project)) {
            $this->container['logger']->debug('MailgunWebhook: ignored => project not found');
            return false;
        }

        // The user must be member of the project
        if (! $this->projectPermission->isMember($project['id'], $user['id'])) {
            $this->container['logger']->debug('MailgunWebhook: ignored => user is not member of the project');
            return false;
        }

        // Get the Markdown contents
        if (empty($payload['stripped-html'])) {
            $description = $payload['stripped-text'];
        }
        else {
            $markdown = new HTML_To_Markdown($payload['stripped-html'], array('strip_tags' => true));
            $description = $markdown->output();
        }

        // Finally, we create the task
        return (bool) $this->taskCreation->create(array(
            'project_id' => $project['id'],
            'title' => $payload['subject'],
            'description' => $description,
            'creator_id' => $user['id'],
        ));
    }

    /**
     * Get the project identifier
     *
     * @access public
     * @param  string  $email
     * @return string
     */
    public function getMailboxHash($email)
    {
        list($local_part,) = explode('@', $email);
        list(,$identifier) = explode('+', $local_part);

        return $identifier;
    }
}
