<?php

namespace Kanboard\Core;

use Kanboard\Core\Filter\FormatterInterface;
use Pimple\Container;
use PicoDb\Table;

/**
 * Paginator Helper
 *
 * @package  Kanboard\Core
 * @author   Frederic Guillot
 */
class Paginator
{
    /**
     * Container instance
     *
     * @access private
     * @var \Pimple\Container
     */
    private $container;

    /**
     * Total number of items
     *
     * @access private
     * @var integer
     */
    private $total = 0;

    /**
     * Page number
     *
     * @access private
     * @var integer
     */
    private $page = 1;

    /**
     * Offset
     *
     * @access private
     * @var integer
     */
    private $offset = 0;

    /**
     * Limit
     *
     * @access private
     * @var integer
     */
    private $limit = 0;

    /**
     * Sort by this column
     *
     * @access private
     * @var string
     */
    private $order = '';

    /**
     * Sorting direction
     *
     * @access private
     * @var string
     */
    private $direction = 'ASC';

    /**
     * Slice of items
     *
     * @access private
     * @var array
     */
    private $items = array();

    /**
     * PicoDb Table instance
     *
     * @access private
     * @var \Picodb\Table
     */
    private $query = null;

    /**
     * Controller name
     *
     * @access private
     * @var string
     */
    private $controller = '';

    /**
     * Action name
     *
     * @access private
     * @var string
     */
    private $action = '';

    /**
     * Url params
     *
     * @access private
     * @var array
     */
    private $params = array();

    /**
     * @var FormatterInterface
     */
    protected $formatter = null;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set a PicoDb query
     *
     * @access public
     * @param  \PicoDb\Table
     * @return $this
     */
    public function setQuery(Table $query)
    {
        $this->query = $query;
        $this->total = $this->query->count();
        return $this;
    }

    /**
     * Set Formatter
     *
     * @param  FormatterInterface $formatter
     * @return $this
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Execute a PicoDb query
     *
     * @access public
     * @return array
     */
    public function executeQuery()
    {
        if ($this->query !== null) {
            $this->query
                ->offset($this->offset)
                ->limit($this->limit)
                ->orderBy($this->order, $this->direction);

            if ($this->formatter !== null) {
                return $this->formatter->withQuery($this->query)->format();
            } else {
                return $this->query->findAll();
            }
        }

        return array();
    }

    /**
     * Set url parameters
     *
     * @access public
     * @param  string      $controller
     * @param  string      $action
     * @param  array       $params
     * @return $this
     */
    public function setUrl($controller, $action, array $params = array())
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->params = $params;
        return $this;
    }

    /**
     * Add manually items
     *
     * @access public
     * @param  array       $items
     * @return $this
     */
    public function setCollection(array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Return the items
     *
     * @access public
     * @return array
     */
    public function getCollection()
    {
        return $this->items ?: $this->executeQuery();
    }

    /**
     * Set the total number of items
     *
     * @access public
     * @param  integer    $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * Get the total number of items
     *
     * @access public
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the default page number
     *
     * @access public
     * @param  integer     $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get the number of current page
     *
     * @access public
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set the default column order
     *
     * @access public
     * @param  string     $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Set the default sorting direction
     *
     * @access public
     * @param  string    $direction
     * @return $this
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * Set the maximum number of items per page
     *
     * @access public
     * @param  integer     $limit
     * @return $this
     */
    public function setMax($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get the maximum number of items per page.
     *
     * @return int
     */
    public function getMax()
    {
        return $this->limit;
    }

    /**
     * Return true if the collection is empty
     *
     * @access public
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->total === 0;
    }

    /**
     * Execute the offset calculation only if the $condition is true
     *
     * @access public
     * @param  boolean    $condition
     * @return $this
     */
    public function calculateOnlyIf($condition)
    {
        if ($condition) {
            $this->calculate();
        }

        return $this;
    }

    /**
     * Calculate the offset value accoring to url params and the page number
     *
     * @access public
     * @return $this
     */
    public function calculate()
    {
        $this->page = $this->container['request']->getIntegerParam('page', 1);
        $this->direction = $this->container['request']->getStringParam('direction', $this->direction);
        $this->order = $this->container['request']->getStringParam('order', $this->order);

        if ($this->page < 1) {
            $this->page = 1;
        }

        $this->offset = (int) (($this->page - 1) * $this->limit);

        return $this;
    }

    /**
     * Get url params for link generation
     *
     * @access public
     * @param  integer  $page
     * @param  string   $order
     * @param  string   $direction
     * @return string
     */
    public function getUrlParams($page, $order, $direction)
    {
        $params = array(
            'page' => $page,
            'order' => $order,
            'direction' => $direction,
        );

        return array_merge($this->params, $params);
    }

    /**
     * Generate the previous link
     *
     * @access public
     * @return string
     */
    public function generatePreviousLink()
    {
        $html = '<span class="pagination-previous">';

        if ($this->offset > 0) {
            $html .= $this->container['helper']->url->link(
                '&larr; '.t('Previous'),
                $this->controller,
                $this->action,
                $this->getUrlParams($this->page - 1, $this->order, $this->direction),
                false,
                'js-modal-replace'
            );
        } else {
            $html .= '&larr; '.t('Previous');
        }

        $html .= '</span>';

        return $html;
    }

    /**
     * Generate the next link
     *
     * @access public
     * @return string
     */
    public function generateNextLink()
    {
        $html = '<span class="pagination-next">';

        if (($this->total - $this->offset) > $this->limit) {
            $html .= $this->container['helper']->url->link(
                t('Next').' &rarr;',
                $this->controller,
                $this->action,
                $this->getUrlParams($this->page + 1, $this->order, $this->direction),
                false,
                'js-modal-replace'
            );
        } else {
            $html .= t('Next').' &rarr;';
        }

        $html .= '</span>';

        return $html;
    }

    /**
     * Generate the page showing.
     *
     * @access public
     * @return string
     */
    public function generatePageShowing()
    {
        return '<span class="pagination-showing">'.t('Showing %d-%d of %d', (($this->getPage() - 1) * $this->getMax() + 1), min($this->getTotal(), $this->getPage() * $this->getMax()), $this->getTotal()).'</span>';
    }

    /**
     * Return true if there is no pagination to show
     *
     * @access public
     * @return boolean
     */
    public function hasNothingToShow()
    {
        return $this->offset === 0 && ($this->total - $this->offset) <= $this->limit;
    }

    /**
     * Generation pagination links
     *
     * @access public
     * @return string
     */
    public function toHtml()
    {
        $html = '';

        if (! $this->hasNothingToShow()) {
            $html .= '<div class="pagination">';
            $html .= $this->generatePageShowing();
            $html .= $this->generatePreviousLink();
            $html .= $this->generateNextLink();
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Magic method to output pagination links
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * Column sorting
     *
     * @param  string   $label         Column title
     * @param  string   $column        SQL column name
     * @return string
     */
    public function order($label, $column)
    {
        $prefix = '';
        $direction = 'ASC';

        if ($this->order === $column) {
            $prefix = $this->direction === 'DESC' ? '&#9660; ' : '&#9650; ';
            $direction = $this->direction === 'DESC' ? 'ASC' : 'DESC';
        }

        return $prefix.$this->container['helper']->url->link(
            $label,
            $this->controller,
            $this->action,
            $this->getUrlParams($this->page, $column, $direction),
            false,
            'js-modal-replace'
        );
    }
}
