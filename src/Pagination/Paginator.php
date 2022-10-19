<?php

declare(strict_types=1);


namespace DMP\RestBundle\Pagination;

use Doctrine\ORM\QueryBuilder;

class Paginator
{
    public function __construct(
        private readonly PaginatorInterface $paginator)
    {}

    public function paginate(QueryBuilder $queryBuilder, Pagination $pagination,
                             bool $fetchJoinCollection = false): Paginated
    {
        return $this->paginator->paginate($queryBuilder, $pagination, $fetchJoinCollection);
    }
}
