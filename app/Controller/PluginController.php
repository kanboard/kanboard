<?php

namespace Kanboard\Controller;

use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Plugin\Directory;
use Kanboard\Core\Plugin\Installer;
use Kanboard\Core\Plugin\PluginInstallerException;

/**
 * Class PluginController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class PluginController extends BaseController
{
    /**
     * Display the plugin page
     *
     * @access public
     */
    public function show()
    {
        $this->response->html($this->helper->layout->plugin('plugin/show', array(
            'plugins' => $this->pluginLoader->getPlugins(),
            'incompatible_plugins' => $this->pluginLoader->getIncompatiblePlugins(),
            'title' => t('Installed Plugins'),
            'is_configured' => Installer::isConfigured(),
        )));
    }

    /**
     * Display list of available plugins
     */
    public function directory()
    {
        $installedPlugins = [];
        $availablePlugins = [];
        $isConfigured = Installer::isConfigured();

        if ($isConfigured) {
            foreach ($this->pluginLoader->getPlugins() as $plugin) {
                $installedPlugins[$plugin->getPluginName()] = $plugin->getPluginVersion();
            }

            $availablePlugins = Directory::getInstance($this->container)->getAvailablePlugins();
        }

        $this->response->html($this->helper->layout->plugin('plugin/directory', array(
            'installed_plugins' => $installedPlugins,
            'available_plugins' => $availablePlugins,
            'title' => t('Plugin Directory'),
            'is_configured' => $isConfigured,
        )));
    }

    /**
     * Install plugin from URL
     *
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     */
    public function install()
    {
        if (! Installer::isConfigured()) {
            throw new AccessForbiddenException();
        }

        $this->checkCSRFParam();
        $pluginArchiveUrl = urldecode($this->request->getStringParam('archive_url'));

        try {
            $installer = new Installer($this->container);
            $installer->install($pluginArchiveUrl);
            $this->flash->success(t('Plugin installed successfully.'));
        } catch (PluginInstallerException $e) {
            $this->flash->failure($e->getMessage());
        }

        $this->response->redirect($this->helper->url->to('PluginController', 'show'));
    }

    /**
     * Update plugin from URL
     *
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     */
    public function update()
    {
        if (! Installer::isConfigured()) {
            throw new AccessForbiddenException();
        }

        $this->checkCSRFParam();
        $pluginArchiveUrl = urldecode($this->request->getStringParam('archive_url'));

        try {
            $installer = new Installer($this->container);
            $installer->update($pluginArchiveUrl);
            $this->flash->success(t('Plugin updated successfully.'));
        } catch (PluginInstallerException $e) {
            $this->flash->failure($e->getMessage());
        }

        $this->response->redirect($this->helper->url->to('PluginController', 'show'));
    }

    /**
     * Confirmation before to remove the plugin
     */
    public function confirm()
    {
        if (! Installer::isConfigured()) {
            throw new AccessForbiddenException();
        }

        $pluginId = $this->request->getStringParam('pluginId');
        $plugins = array_merge(
            $this->pluginLoader->getPlugins(),
            $this->pluginLoader->getIncompatiblePlugins()
        );

        $this->response->html($this->template->render('plugin/remove', array(
            'plugin_id' => $pluginId,
            'plugin' => $plugins[$pluginId],
        )));
    }

    /**
     * Remove a plugin
     *
     * @throws \Kanboard\Core\Controller\AccessForbiddenException
     */
    public function uninstall()
    {
        if (! Installer::isConfigured()) {
            throw new AccessForbiddenException();
        }

        $this->checkCSRFParam();
        $pluginId = $this->request->getStringParam('pluginId');

        try {
            $installer = new Installer($this->container);
            $installer->uninstall($pluginId);
            $this->flash->success(t('Plugin removed successfully.'));
        } catch (PluginInstallerException $e) {
            $this->flash->failure($e->getMessage());
        }

        $this->response->redirect($this->helper->url->to('PluginController', 'show'));
    }
}
