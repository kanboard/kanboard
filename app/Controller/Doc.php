<?php

namespace Controller;

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
        list($title,, $content) = explode("\n", $data, 3);

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
        $filename = $this->request->getStringParam('file', 'index');

        if (! preg_match('/^[a-z0-9\-]+/', $filename)) {
            $filename = 'index';
        }

        $filename = __DIR__.'/../../doc/'.$filename.'.markdown';

        if (! file_exists($filename)) {
            $filename = __DIR__.'/../../doc/index.markdown';
        }

        $this->response->html($this->template->layout('doc/show', $this->readFile($filename) + array(
            'board_selector' => $this->projectPermission->getAllowedProjects($this->userSession->getId()),
        )));
    }
}
