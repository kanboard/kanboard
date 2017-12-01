<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Class ModalHelper
 *
 * @package Kanboard\Helper
 * @author  Frederic Guillot
 */
class ModalHelper extends Base
{
    public function submitButtons(array $params = array())
    {
        return $this->helper->app->component('submit-buttons', array(
            'submitLabel' => isset($params['submitLabel']) ? $params['submitLabel'] : t('Save'),
            'orLabel'     => t('or'),
            'cancelLabel' => t('cancel'),
            'color'       => isset($params['color']) ? $params['color'] : 'blue',
            'tabindex'    => isset($params['tabindex']) ? $params['tabindex'] : null,
            'disabled'    => isset($params['disabled']) ? true : false,
        ));
    }

    public function confirmButtons($controller, $action, array $params = array(), $submitLabel = '', $tabindex = null)
    {
        return $this->helper->app->component('confirm-buttons', array(
            'url'         => $this->helper->url->href($controller, $action, $params, true),
            'submitLabel' => $submitLabel ?: t('Yes'),
            'orLabel'     => t('or'),
            'cancelLabel' => t('cancel'),
            'tabindex'    => $tabindex,
        ));
    }

    public function largeIcon($icon, $label, $controller, $action, array $params = array())
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw js-modal-large" aria-hidden="true"></i>';
        return $this->helper->url->link($html, $controller, $action, $params, false, 'js-modal-large', $label);
    }

    public function large($icon, $label, $controller, $action, array $params = array())
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw js-modal-large" aria-hidden="true"></i>'.$label;
        return $this->helper->url->link($html, $controller, $action, $params, false, 'js-modal-large');
    }

    public function medium($icon, $label, $controller, $action, array $params = array(), $title = '')
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw js-modal-medium" aria-hidden="true"></i>'.$label;
        return $this->helper->url->link($html, $controller, $action, $params, false, 'js-modal-medium', $title);
    }

    public function small($icon, $label, $controller, $action, array $params = array())
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw js-modal-small" aria-hidden="true"></i>'.$label;
        return $this->helper->url->link($html, $controller, $action, $params, false, 'js-modal-small');
    }

    public function mediumButton($icon, $label, $controller, $action, array $params = array())
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw js-modal-medium" aria-hidden="true"></i>'.$label;
        return $this->helper->url->link($html, $controller, $action, $params, false, 'js-modal-medium btn');
    }

    public function mediumIcon($icon, $label, $controller, $action, array $params = array())
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw js-modal-medium" aria-hidden="true"></i>';
        return $this->helper->url->link($html, $controller, $action, $params, false, 'js-modal-medium', $label);
    }

    public function confirm($icon, $label, $controller, $action, array $params = array())
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw js-modal-confirm" aria-hidden="true"></i>'.$label;
        return $this->helper->url->link($html, $controller, $action, $params, false, 'js-modal-confirm');
    }

    public function confirmLink($label, $controller, $action, array $params = array())
    {
        return $this->helper->url->link($label, $controller, $action, $params, false, 'js-modal-confirm');
    }

    public function replaceLink($label, $controller, $action, array $params = array())
    {
        return $this->helper->url->link($label, $controller, $action, $params, false, 'js-modal-replace');
    }

    public function replaceIconLink($icon, $label, $controller, $action, array $params = array())
    {
        $html = '<i class="fa fa-'.$icon.' fa-fw js-modal-replace" aria-hidden="true"></i>'.$label;
        return $this->helper->url->link($html, $controller, $action, $params, false, 'js-modal-replace');
    }
}
