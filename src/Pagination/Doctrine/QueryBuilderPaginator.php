<?php

declare(strict_types=1);

namespace DMP\RestBundle\Pagination\Doctrine;

use DMP\RestBundle\Pagination\PaginatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DMP\RestBundle\Pagination\Paginated;
use DMP\RestBundle\Pagination\Pagination;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use ArrayIterator;
use Exception;

class QueryBuilderPaginator implements PaginatorInterface
{

    /**
     * @throws Exception
     */
    public function paginate(QueryBuilder $queryBuilder, Pagination $paginationParameters,
                                bool $fetchJoinCollection, ?callable $callback): Paginated
    {
	    $queryBuilder->getQuery()->setHint(CountWalker::HINT_DISTINCT, false);

        if ($paginationParameters->getLimit() !== 0) {
            $queryBuilder
                ->setFirstResult(($paginationParameters->getPage() - 1) * $paginationParameters->getLimit())
                ->setMaxResults($paginationParameters->getLimit())
            ;
        }

        $paginator = new Paginator($queryBuilder, $fetchJoinCollection);

        return new Paginated(
            $paginator->count(),
            $paginationParameters->getLimit(),
            $paginationParameters->getPage(),
            $this->getPageResult($paginator->getIterator(), $callback),
	        $this->getTotalPages($paginator->count(), $paginationParameters->getLimit()),
	        $this->getPreviousPage($paginationParameters->getPage()),
	        $this->getNextPage(
		        $paginationParameters->getPage(),
                $this->getTotalPages($paginator->count(), $paginationParameters->getLimit()),
		        $paginator->count(),
		        $paginationParameters->getLimit(),
	        )
        );
    }

    private function getPageResult(ArrayIterator $iterator, ?callable $callback): ArrayCollection
    {
        $result = iterator_to_array($iterator);
        if ($callback === null) {
            return new ArrayCollection($result);
        }

        return new ArrayCollection(array_values(array_map($callback, $result)));
    }

    private function getTotalPages(int $count, int $limit): int
    {
        return $limit > 0 ? (int)ceil($count / $limit) : 1;
    }

    private function getPreviousPage(int $currentPage): ?int
    {
    	if ($currentPage === 1) {
    		return null;
	    }
	    return $currentPage - 1;
    }

	private function getNextPage(int $currentPage, int $totalPages, int $totalItems, int $itemsOnPage): ?int
	{
		if ($currentPage === $totalPages || $totalItems <= $itemsOnPage) {
			return null;
		}
		return $currentPage + 1;
	}
}
