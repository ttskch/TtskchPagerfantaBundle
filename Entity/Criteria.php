<?php

namespace Ttskch\PagerfantaBundle\Entity;

class Criteria
{
    /**
     * @var int|string
     */
    public $page;

    /**
     * @var int|string
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

    private $form;

    /**
     * @param int|string $defaultLimit
     * @param string $defaultSortKey
     * @param string $defaultSortDirection
     */
    final public function __construct($defaultLimit, $defaultSortKey, $defaultSortDirection)
    {
        $this->page = 1;
        $this->limit = $defaultLimit;
        $this->sort = $defaultSortKey;
        $this->direction = $defaultSortDirection;
    }
}
