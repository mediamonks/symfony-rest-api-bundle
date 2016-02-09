<?php

namespace MediaMonks\RestApiBundle\Response;

interface PaginatedResponseInterface
{
    public function toArray();

    public function getData();
}
