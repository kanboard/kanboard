<?php

namespace Kanboard\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BootstrapSubscriber extends \Kanboard\Core\Base implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'app.bootstrap' => array('setup', 0),
        );
    }

    public function setup()
    {
        $this->config->setupTranslations();
        $this->config->setupTimezone();
        $this->sessionStorage->hasSubtaskInProgress = $this->subtask->hasSubtaskInProgress($this->userSession->getId());
    }

    public function __destruct()
    {
        if (DEBUG) {
            foreach ($this->db->getLogMessages() as $message) {
                $this->logger->debug($message);
            }

            $this->logger->debug('SQL_QUERIES={nb}', array('nb' => $this->container['db']->nbQueries));
            $this->logger->debug('RENDERING={time}', array('time' => microtime(true) - $this->request->getStartTime()));
            $this->logger->debug('MEMORY='.$this->helper->text->bytes(memory_get_usage()));
            $this->logger->debug('URI='.$this->request->getUri());
            $this->logger->debug('###############################################');
        }
    }
}
