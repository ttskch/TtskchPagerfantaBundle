<?php

namespace Ttskch\PagerfantaBundle\Form;

class PaginationCriteria
{
    /**
     * @var string
     */
    public $page;

    /**
     * @var string
     */
    public $limit;

    /**
     * @var string
     */
    public $sort;

    /**
     * @var string
     */
    public $direction;

    /**
     * @param string $defaultLimit
     * @param string $defaultSortKey
     * @param string $defaultSortDirection
     */
    public function __construct($defaultLimit, $defaultSortKey, $defaultSortDirection)
    {
        $this->page = 1;
        $this->limit = $defaultLimit;
        $this->sort = $defaultSortKey;
        $this->direction = $defaultSortDirection;
    }
}
