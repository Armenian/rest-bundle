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

//CREATE FUNCTION estimate_count(query text) RETURNS integer AS $$
//DECLARE
//rec   record;
//    rows  integer;
//BEGIN
//    FOR rec IN EXECUTE 'EXPLAIN ' || query LOOP
//            rows := substring(rec."QUERY PLAN" FROM ' rows=([[:digit:]]+)');
//            EXIT WHEN rows IS NOT NULL;
//        END LOOP;
//    RETURN rows;
//END;
//$$ LANGUAGE plpgsql VOLATILE STRICT;

//SELECT estimate_count('SELECT 1 FROM table_name WHERE id > 100 ')
class EstimateCountPaginator extends Paginator
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
