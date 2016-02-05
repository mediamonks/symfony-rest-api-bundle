<?php

namespace MediaMonks\RestApiBundle\Response;

abstract class AbstractPaginatedResponse
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $total;

    /**
     * PaginatedResponseAbstract constructor.
     * @param $data
     * @param $limit
     * @param null $total
     */
    public function __construct($data, $limit, $total = null)
    {
        $this->data  = $data;
        $this->limit = $limit;
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }
}
