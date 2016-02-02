<?php

namespace MediaMonks\RestApiBundle\Response;

class OffsetPaginatedResponse extends PaginatedResponseAbstract
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @param $data
     * @param $offset
     * @param $limit
     * @param null $total
     */
    public function __construct($data, $offset, $limit, $total = null)
    {
        $this->data   = $data;
        $this->offset = $offset;
        $this->limit  = $limit;
        $this->total  = $total;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function toArray()
    {
        $data = [
            'offset' => $this->getOffset(),
            'limit'  => $this->getLimit()
        ];
        if (!is_null($this->getTotal())) {
            $data['total'] = $this->getTotal();
        }

        return $data;
    }
}
