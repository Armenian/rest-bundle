<?php

declare(strict_types=1);


namespace DMP\RestBundle\Pagination\Paginator;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use function array_map;
use function array_sum;

class OptimizedPaginator extends Paginator
{
    private QueryBuilder $originQuery;

    public function __construct(QueryBuilder $queryBuilder, Query|QueryBuilder $query, bool $fetchJoinCollection = true)
    {
        $this->originQuery = $queryBuilder;
        parent::__construct($query, $fetchJoinCollection);
    }

    private ?int $count = null;

    public function count(): int
    {
        if ($this->count === null) {
            try {
                $where = $this->originQuery->getDQLPart('where');
                if (null !== $where) {
                    return parent::count();
                }

                $from = $this->originQuery->getDQLPart('from');
                if (null === $from) {
                    return parent::count();
                }
                $tableName = $from[0]->getAlias();

                $rsm = new ResultSetMapping();
                $rsm->addScalarResult('count', 'count', 'bigint');
                $query = $this->getQuery()->getEntityManager()->createNativeQuery(
                    'SELECT reltuples::bigint AS count FROM  pg_class WHERE oid = to_regclass(\'public.' . $tableName . '\')',
                    $rsm
                );
                $this->count = (int) array_sum(array_map('current', $query->getScalarResult()));
            } catch (NoResultException $e) {
                $this->count = 0;
            }
        }

        return $this->count;
    }
}
