<?php

namespace Kanboard\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BootstrapSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'app.bootstrap' => 'execute',
        );
    }

    public function execute()
    {
        $this->logger->debug('Subscriber executed: '.__METHOD__);
        $this->languageModel->loadCurrentLanguage();
        $this->timezoneModel->setCurrentTimezone();
        $this->actionManager->attachEvents();

        if ($this->userSession->isLogged()) {
            session_set('hasSubtaskInProgress', $this->subtaskStatusModel->hasSubtaskInProgress($this->userSession->getId()));
        }
    }

    public function __destruct()
    {
        if (DEBUG) {
            foreach ($this->db->getLogMessages() as $message) {
                $this->logger->debug('SQL: ' . $message);
            }

            $this->logger->debug('APP: nb_queries={nb}', array('nb' => $this->db->getStatementHandler()->getNbQueries()));
            $this->logger->debug('APP: rendering_time={time}', array('time' => microtime(true) - $this->request->getStartTime()));
            $this->logger->debug('APP: memory_usage='.$this->helper->text->bytes(memory_get_usage()));
            $this->logger->debug('APP: uri='.$this->request->getUri());
            $this->logger->debug('###############################################');
        }
    }
}
