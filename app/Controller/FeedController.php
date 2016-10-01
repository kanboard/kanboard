<?php

namespace Kanboard\Controller;

use DateTime;
use Kanboard\Core\Controller\AccessForbiddenException;
use PicoFeed\Syndication\AtomFeedBuilder;
use PicoFeed\Syndication\AtomItemBuilder;
use PicoFeed\Syndication\FeedBuilder;

/**
 * Atom/RSS Feed controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class FeedController extends BaseController
{
    /**
     * RSS feed for a user
     *
     * @access public
     */
    public function user()
    {
        $token = $this->request->getStringParam('token');
        $user = $this->userModel->getByToken($token);

        // Token verification
        if (empty($user)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $events = $this->helper->projectActivity->getProjectsEvents($this->projectPermissionModel->getActiveProjectIds($user['id']));

        $feedBuilder = AtomFeedBuilder::create()
            ->withTitle(e('Project activities for %s', $this->helper->user->getFullname($user)))
            ->withFeedUrl($this->helper->url->to('FeedController', 'user', array('token' => $user['token']), '', true))
            ->withSiteUrl($this->helper->url->base())
            ->withDate(new DateTime());

        $this->response->xml($this->buildFeedItems($events, $feedBuilder)->build());
    }

    /**
     * RSS feed for a project
     *
     * @access public
     */
    public function project()
    {
        $token = $this->request->getStringParam('token');
        $project = $this->projectModel->getByToken($token);

        if (empty($project)) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }

        $events = $this->helper->projectActivity->getProjectEvents($project['id']);

        $feedBuilder = AtomFeedBuilder::create()
            ->withTitle(e('%s\'s activity', $project['name']))
            ->withFeedUrl($this->helper->url->to('FeedController', 'project', array('token' => $project['token']), '', true))
            ->withSiteUrl($this->helper->url->base())
            ->withDate(new DateTime());

        $this->response->xml($this->buildFeedItems($events, $feedBuilder)->build());
    }

    /**
     * Build feed items
     *
     * @access protected
     * @param  array       $events
     * @param  FeedBuilder $feedBuilder
     * @return FeedBuilder
     */
    protected function buildFeedItems(array $events, FeedBuilder $feedBuilder)
    {
        foreach ($events as $event) {
            $itemDate = new DateTime();
            $itemDate->setTimestamp($event['date_creation']);

            $itemUrl = $this->helper->url->to('TaskViewController', 'show', array('task_id' => $event['task_id']), '', true);

            $feedBuilder
                ->withItem(AtomItemBuilder::create($feedBuilder)
                    ->withTitle($event['event_title'])
                    ->withUrl($itemUrl.'#event-'.$event['id'])
                    ->withAuthor($event['author'])
                    ->withPublishedDate($itemDate)
                    ->withUpdatedDate($itemDate)
                    ->withContent($event['event_content'])
                );
        }

        return $feedBuilder;
    }
}
