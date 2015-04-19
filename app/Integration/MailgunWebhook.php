<?php

namespace Integration;

use HTML_To_Markdown;
use Core\Tool;

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
        if (empty($payload['sender']) || empty($payload['subject']) || empty($payload['recipient'])) {
            return false;
        }

        // The user must exists in Kanboard
        $user = $this->user->getByEmail($payload['sender']);

        if (empty($user)) {
            $this->container['logger']->debug('MailgunWebhook: ignored => user not found');
            return false;
        }

        // The project must have a short name
        $project = $this->project->getByIdentifier(Tool::getMailboxHash($payload['recipient']));

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
        if (! empty($payload['stripped-html'])) {
            $markdown = new HTML_To_Markdown($payload['stripped-html'], array('strip_tags' => true));
            $description = $markdown->output();
        }
        else if (! empty($payload['stripped-text'])) {
            $description = $payload['stripped-text'];
        }
        else {
            $description = '';
        }

        // Finally, we create the task
        return (bool) $this->taskCreation->create(array(
            'project_id' => $project['id'],
            'title' => $payload['subject'],
            'description' => $description,
            'creator_id' => $user['id'],
        ));
    }
}
