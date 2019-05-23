<?php

namespace Ttskch\PagerfantaBundle\Entity;

class Criteria
{
    /**
     * @var int
     */
    public $page;

    /**
     * @var int
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

    final public function __construct(int $defaultLimit, string $defaultSortKey, string $defaultSortDirection)
    {
        $this->page = 1;
        $this->limit = $defaultLimit;
        $this->sort = $defaultSortKey;
        $this->direction = $defaultSortDirection;
    }
}
