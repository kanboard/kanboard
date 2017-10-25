<?php

namespace JsonRPC\Request;

/**
 * Class RequestBuilder
 *
 * @package JsonRPC\Request
 * @author  Frederic Guillot
 */
class RequestBuilder
{
    /**
     * Request ID
     *
     * @access private
     * @var mixed
     */
    private $id = null;

    /**
     * Method name
     *
     * @access private
     * @var string
     */
    private $procedure = '';

    /**
     * Method arguments
     *
     * @access private
     * @var array
     */
    private $params = array();

    /**
     * Additional request attributes
     *
     * @access private
     * @var array
     */
    private $reqattrs = array();

    /**
     * Get new object instance
     *
     * @static
     * @access public
     * @return RequestBuilder
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set id
     *
     * @access public
     * @param  null $id
     * @return RequestBuilder
     */
    public function withId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set method
     *
     * @access public
     * @param  string $procedure
     * @return RequestBuilder
     */
    public function withProcedure($procedure)
    {
        $this->procedure = $procedure;
        return $this;
    }

    /**
     * Set parameters
     *
     * @access public
     * @param  array $params
     * @return RequestBuilder
     */
    public function withParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Set additional request attributes
     *
     * @access public
     * @param  array $reqattrs
     * @return RequestBuilder
     */
    public function withRequestAttributes(array $reqattrs)
    {
        $this->reqattrs = $reqattrs;
        return $this;
    }

    /**
     * Build the payload
     *
     * @access public
     * @return string
     */
    public function build()
    {
        $payload = array_merge_recursive($this->reqattrs, array(
            'jsonrpc' => '2.0',
            'method' => $this->procedure,
            'id' => $this->id ?: mt_rand(),
        ));

        if (! empty($this->params)) {
            $payload['params'] = $this->params;
        }

        return json_encode($payload);
    }
}
