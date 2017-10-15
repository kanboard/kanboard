<?php

namespace Kanboard\Core\ExternalTask;

/**
 * Class ExternalTaskManager
 *
 * @package Kanboard\Core\ExternalTask
 * @author  Frederic Guillot
 */
class ExternalTaskManager
{
    protected $providers = array();

    /**
     * Register a new task provider
     *
     * @param ExternalTaskProviderInterface $externalTaskProvider
     * @return $this
     */
    public function register(ExternalTaskProviderInterface $externalTaskProvider)
    {
        $this->providers[$externalTaskProvider->getName()] = $externalTaskProvider;
        return $this;
    }

    /**
     * Get task provider
     *
     * @param  string $name
     * @return ExternalTaskProviderInterface|null
     * @throws ProviderNotFoundException
     */
    public function getProvider($name)
    {
        if (isset($this->providers[$name])) {
            return $this->providers[$name];
        }

        throw new ProviderNotFoundException('Unable to load this provider: '.$name);
    }

    /**
     * Get list of task providers
     *
     * @return array
     */
    public function getProvidersList()
    {
        $providers = array_keys($this->providers);

        if (count($providers)) {
            return array_combine($providers, $providers);
        }

        return array();
    }

    /**
     * Get all providers
     *
     * @return ExternalTaskProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
