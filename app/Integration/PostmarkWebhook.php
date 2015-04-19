<?php

namespace Integration;

use HTML_To_Markdown;

/**
 * Postmark Webhook
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class PostmarkWebhook extends Base
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
        if (empty($payload['From']) || empty($payload['Subject']) || empty($payload['MailboxHash']) || empty($payload['TextBody'])) {
            return false;
        }

        // The user must exists in Kanboard
        $user = $this->user->getByEmail($payload['From']);

        if (empty($user)) {
            $this->container['logger']->debug('PostmarkWebhook: ignored => user not found');
            return false;
        }

        // The project must have a short name
        $project = $this->project->getByIdentifier($payload['MailboxHash']);

        if (empty($project)) {
            $this->container['logger']->debug('PostmarkWebhook: ignored => project not found');
            return false;
        }

        // The user must be member of the project
        if (! $this->projectPermission->isMember($project['id'], $user['id'])) {
            $this->container['logger']->debug('PostmarkWebhook: ignored => user is not member of the project');
            return false;
        }

        // Get the Markdown contents
        if (empty($payload['HtmlBody'])) {
            $description = $payload['TextBody'];
        }
        else {
            $markdown = new HTML_To_Markdown($payload['HtmlBody'], array('strip_tags' => true));
            $description = $markdown->output();
        }

        // Finally, we create the task
        return (bool) $this->taskCreation->create(array(
            'project_id' => $project['id'],
            'title' => $payload['Subject'],
            'description' => $description,
            'creator_id' => $user['id'],
        ));
    }
}
