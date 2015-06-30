<?php

namespace Integration;

use HTML_To_Markdown;
use Core\Tool;

/**
 * Sendgrid Integration
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class Sendgrid extends \Core\Base
{
    /**
     * Send a HTML email
     *
     * @access public
     * @param  string  $email
     * @param  string  $name
     * @param  string  $subject
     * @param  string  $html
     * @param  string  $author
     */
    public function sendEmail($email, $name, $subject, $html, $author)
    {
        $payload = array(
            'api_user' => SENDGRID_API_USER,
            'api_key' => SENDGRID_API_KEY,
            'to' => $email,
            'toname' => $name,
            'from' => MAIL_FROM,
            'fromname' => $author,
            'html' => $html,
            'subject' => $subject,
        );

        $this->httpClient->postForm('https://api.sendgrid.com/api/mail.send.json', $payload);
    }

    /**
     * Parse incoming email
     *
     * @access public
     * @param  array   $payload   Incoming email
     * @return boolean
     */
    public function receiveEmail(array $payload)
    {
        if (empty($payload['envelope']) || empty($payload['subject'])) {
            return false;
        }

        $envelope = json_decode($payload['envelope'], true);
        $sender = isset($envelope['to'][0]) ? $envelope['to'][0] : '';

        // The user must exists in Kanboard
        $user = $this->user->getByEmail($envelope['from']);

        if (empty($user)) {
            $this->container['logger']->debug('SendgridWebhook: ignored => user not found');
            return false;
        }

        // The project must have a short name
        $project = $this->project->getByIdentifier(Tool::getMailboxHash($sender));

        if (empty($project)) {
            $this->container['logger']->debug('SendgridWebhook: ignored => project not found');
            return false;
        }

        // The user must be member of the project
        if (! $this->projectPermission->isMember($project['id'], $user['id'])) {
            $this->container['logger']->debug('SendgridWebhook: ignored => user is not member of the project');
            return false;
        }

        // Get the Markdown contents
        if (! empty($payload['html'])) {
            $markdown = new HTML_To_Markdown($payload['html'], array('strip_tags' => true));
            $description = $markdown->output();
        }
        else if (! empty($payload['text'])) {
            $description = $payload['text'];
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
