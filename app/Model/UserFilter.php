<?php

namespace Kanboard\Model;

/**
 * User Filter
 *
 * @package  model
 * @author   Frederic Guillot
 */
class UserFilter extends Base
{
    /**
     * Search query
     *
     * @access private
     * @var string
     */
    private $input;

    /**
     * Query
     *
     * @access protected
     * @var \PicoDb\Table
     */
    protected $query;

    /**
     * Initialize filter
     *
     * @access  public
     * @param   string $input
     * @return  UserFilter
     */
    public function create($input)
    {
        $this->query = $this->db->table(User::TABLE);
        $this->input = $input;
        return $this;
    }

    /**
     * Filter users by name or username
     *
     * @access  public
     * @return  UserFilter
     */
    public function filterByUsernameOrByName()
    {
        $this->query->beginOr()
            ->ilike('username', '%'.$this->input.'%')
            ->ilike('name', '%'.$this->input.'%')
            ->closeOr();

        return $this;
    }

    /**
     * Get all results of the filter
     *
     * @access public
     * @return array
     */
    public function findAll()
    {
        return $this->query->findAll();
    }

    /**
     * Get the PicoDb query
     *
     * @access public
     * @return \PicoDb\Table
     */
    public function getQuery()
    {
        return $this->query;
    }
}
