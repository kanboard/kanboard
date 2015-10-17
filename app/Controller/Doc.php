<?php

namespace Kanboard\Controller;

use Parsedown;

/**
 * Documentation controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Doc extends Base
{
    private function readFile($filename)
    {
        $url = $this->helper->url;
        $data = file_get_contents($filename);
        list($title, ) = explode("\n", $data, 2);

        $replaceUrl = function (array $matches) use ($url) {
            return '('.$url->to('doc', 'show', array('file' => str_replace('.markdown', '', $matches[1]))).')';
        };

        $content = preg_replace_callback('/\((.*.markdown)\)/', $replaceUrl, $data);

        return array(
            'content' => Parsedown::instance()->text($content),
            'title' => $title !== 'Documentation' ? t('Documentation: %s', $title) : $title,
        );
    }

    public function show()
    {
        $page = $this->request->getStringParam('file', 'index');

        if (! preg_match('/^[a-z0-9\-]+/', $page)) {
            $page = 'index';
        }

        $filenames = array(__DIR__.'/../../doc/'.$page.'.markdown');
        $filename = __DIR__.'/../../doc/index.markdown';

        if ($this->config->getCurrentLanguage() === 'fr_FR') {
            array_unshift($filenames, __DIR__.'/../../doc/fr/'.$page.'.markdown');
        }

        foreach ($filenames as $file) {
            if (file_exists($file)) {
                $filename = $file;
                break;
            }
        }

        $this->response->html($this->template->layout('doc/show', $this->readFile($filename) + array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
        )));
    }
}
