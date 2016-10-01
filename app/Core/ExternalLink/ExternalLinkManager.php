<?php

namespace Kanboard\Core\ExternalLink;

use Kanboard\Core\Base;

/**
 * External Link Manager
 *
 * @package  externalLink
 * @author   Frederic Guillot
 */
class ExternalLinkManager extends Base
{
    /**
     * Automatic type value
     *
     * @var string
     */
    const TYPE_AUTO = 'auto';

    /**
     * Registered providers
     *
     * @access private
     * @var ExternalLinkProviderInterface[]
     */
    private $providers = array();

    /**
     * Type chosen by the user
     *
     * @access private
     * @var string
     */
    private $userInputType = '';

    /**
     * Text entered by the user
     *
     * @access private
     * @var string
     */
    private $userInputText = '';

    /**
     * Register a new provider
     *
     * Providers are registered in a LIFO queue
     *
     * @access public
     * @param  ExternalLinkProviderInterface $provider
     * @return ExternalLinkManager
     */
    public function register(ExternalLinkProviderInterface $provider)
    {
        array_unshift($this->providers, $provider);
        return $this;
    }

    /**
     * Get provider
     *
     * @access public
     * @param  string $type
     * @throws ExternalLinkProviderNotFound
     * @return ExternalLinkProviderInterface
     */
    public function getProvider($type)
    {
        foreach ($this->providers as $provider) {
            if ($provider->getType() === $type) {
                return $provider;
            }
        }

        throw new ExternalLinkProviderNotFound('Unable to find link provider: '.$type);
    }

    /**
     * Get link types
     *
     * @access public
     * @return array
     */
    public function getTypes()
    {
        $types = array();

        foreach ($this->providers as $provider) {
            $types[$provider->getType()] = $provider->getName();
        }

        asort($types);

        return array(self::TYPE_AUTO => t('Auto')) + $types;
    }

    /**
     * Get dependency label from a provider
     *
     * @access public
     * @param  string $type
     * @param  string $dependency
     * @return string
     */
    public function getDependencyLabel($type, $dependency)
    {
        $provider = $this->getProvider($type);
        $dependencies = $provider->getDependencies();
        return isset($dependencies[$dependency]) ? $dependencies[$dependency] : $dependency;
    }

    /**
     * Find a provider that match
     *
     * @access public
     * @throws ExternalLinkProviderNotFound
     * @return ExternalLinkProviderInterface
     */
    public function find()
    {
        if ($this->userInputType === self::TYPE_AUTO) {
            $provider = $this->findProvider();
        } else {
            $provider = $this->getProvider($this->userInputType);
            $provider->setUserTextInput($this->userInputText);

            if (! $provider->match()) {
                throw new ExternalLinkProviderNotFound('Unable to parse URL with selected provider');
            }
        }

        if ($provider === null) {
            throw new ExternalLinkProviderNotFound('Unable to find link information from provided information');
        }

        return $provider;
    }

    /**
     * Set form values
     *
     * @access public
     * @param  array $values
     * @return ExternalLinkManager
     */
    public function setUserInput(array $values)
    {
        $this->userInputType = empty($values['type']) ? self::TYPE_AUTO : $values['type'];
        $this->userInputText = empty($values['text']) ? '' : trim($values['text']);
        return $this;
    }

    /**
     * Set provider type
     *
     * @access public
     * @param  string $userInputType
     * @return ExternalLinkManager
     */
    public function setUserInputType($userInputType)
    {
        $this->userInputType = $userInputType;
        return $this;
    }

    /**
     * Set external link
     * @param  string $userInputText
     * @return ExternalLinkManager
     */
    public function setUserInputText($userInputText)
    {
        $this->userInputText = $userInputText;
        return $this;
    }

    /**
     * Find a provider that user input
     *
     * @access private
     * @return ExternalLinkProviderInterface
     */
    private function findProvider()
    {
        foreach ($this->providers as $provider) {
            $provider->setUserTextInput($this->userInputText);

            if ($provider->match()) {
                return $provider;
            }
        }

        return null;
    }
}
