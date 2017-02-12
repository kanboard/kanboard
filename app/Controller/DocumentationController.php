<?php

namespace Kanboard\Controller;

use Parsedown;

/**
 * Documentation Viewer
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class DocumentationController extends BaseController
{
    public function show()
    {
        $page = $this->request->getStringParam('file', 'index');

        if (!preg_match('/^[a-z0-9\-]+/', $page)) {
            $page = 'index';
        }

        $filename = $this->getPageFilename($page);
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
        return '('.$this->helper->url->to('DocumentationController', 'show', array('file' => str_replace('.markdown', '', $matches[1]))).')';
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
        return '('.$this->getFileBaseUrl($matches[1]).')';
    }

    /**
     * Get Markdown file according to the current language
     *
     * @access private
     * @param  string $page
     * @return string
     */
    private function getPageFilename($page)
    {
        return $this->getFileLocation($page . '.markdown') ?:
            implode(DIRECTORY_SEPARATOR, array(ROOT_DIR, 'doc', 'en_US', 'index.markdown'));
    }

    /**
     * Get base URL for Markdown links
     *
     * @access private
     * @param  string $filename
     * @return string
     */
    private function getFileBaseUrl($filename)
    {
        $language = $this->languageModel->getCurrentLanguage();
        $path = $this->getFileLocation($filename);

        if (strpos($path, $language) !== false) {
            $url = implode('/', array('doc', $language, $filename));
        } else {
            $url = implode('/', array('doc', $filename));
        }

        return $this->helper->url->base().$url;
    }

    /**
     * Get file location according to the current language
     *
     * @access private
     * @param  string $filename
     * @return string
     */
    private function getFileLocation($filename)
    {
        $files = array(
            implode(DIRECTORY_SEPARATOR, array(ROOT_DIR, 'doc', $this->languageModel->getCurrentLanguage(), $filename)),
            implode(DIRECTORY_SEPARATOR, array(ROOT_DIR, 'doc', 'en_US', $filename)),
        );

        foreach ($files as $filename) {
            if (file_exists($filename)) {
                return $filename;
            }
        }

        return '';
    }
}
