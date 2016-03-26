<?php

namespace Kanboard\Controller;

use Parsedown;

/**
 * Documentation Viewer
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class Doc extends Base
{
    public function show()
    {
        $page = $this->request->getStringParam('file', 'index');

        if (!preg_match('/^[a-z0-9\-]+/', $page)) {
            $page = 'index';
        }

        if ($this->config->getCurrentLanguage() === 'fr_FR') {
            $filename = __DIR__.'/../../doc/fr/' . $page . '.markdown';
        } else {
            $filename = __DIR__ . '/../../doc/' . $page . '.markdown';
        }

        if (!file_exists($filename)) {
            $filename = __DIR__.'/../../doc/index.markdown';
        }

        $this->response->html($this->helper->layout->app('doc/show', $this->render($filename)));
    }

    /**
     * Display keyboard shortcut
     */
    public function shortcuts()
    {
        $this->response->html($this->template->render('config/keyboard_shortcuts'));
    }

    /**
     * Prepare Markdown file
     *
     * @access private
     * @param  string $filename
     * @return array
     */
    private function render($filename)
    {
        $data = file_get_contents($filename);
        $content = preg_replace_callback('/\((.*.markdown)\)/', array($this, 'replaceMarkdownUrl'), $data);
        $content = preg_replace_callback('/\((screenshots.*\.png)\)/', array($this, 'replaceImageUrl'), $content);

        list($title, ) = explode("\n", $data, 2);

        return array(
            'content' => Parsedown::instance()->text($content),
            'title' => $title !== 'Documentation' ? t('Documentation: %s', $title) : $title,
        );
    }

    /**
     * Regex callback to replace Markdown links
     *
     * @access public
     * @param  array $matches
     * @return string
     */
    public function replaceMarkdownUrl(array $matches)
    {
        return '('.$this->helper->url->to('doc', 'show', array('file' => str_replace('.markdown', '', $matches[1]))).')';
    }

    /**
     * Regex callback to replace image links
     *
     * @access public
     * @param  array $matches
     * @return string
     */
    public function replaceImageUrl(array $matches)
    {
        if ($this->config->getCurrentLanguage() === 'fr_FR') {
            return '('.$this->helper->url->base().'doc/fr/'.$matches[1].')';
        }

        return '('.$this->helper->url->base().'doc/'.$matches[1].')';
    }
}
