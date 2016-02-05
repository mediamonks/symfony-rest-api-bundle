<?php

namespace MediaMonks\RestApiBundle\Response;

class CursorPaginatedResponse extends AbstractPaginatedResponse implements PaginatedResponseInterface
{
    /**
     * @var mixed
     */
    protected $before;

    /**
     * @var mixed
     */
    protected $after;

    /**
     * @param $data
     * @param $before
     * @param $after
     * @param $limit
     * @param null $total
     */
    public function __construct($data, $before, $after, $limit, $total = null)
    {
        parent::__construct($data, $limit, $total);
        $this->before = $before;
        $this->after  = $after;
    }

    /**
     * @return mixed
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @param mixed $before
     */
    public function setBefore($before)
    {
        $this->before = $before;
    }

    /**
     * @return mixed
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * @param mixed $after
     */
    public function setAfter($after)
    {
        $this->after = $after;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'before' => $this->getBefore(),
            'after'  => $this->getAfter(),
            'limit'  => $this->getLimit()
        ];
        if (!is_null($this->getTotal())) {
            $data['total'] = $this->getTotal();
        }

        return $data;
    }
}
