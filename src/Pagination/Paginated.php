<?php

declare(strict_types=1);

namespace DMP\RestBundle\Pagination;

use Doctrine\Common\Collections\Collection;

class Paginated implements PaginatedCollection, \JsonSerializable
{
    public function __construct(
        private readonly int $totalCount,
        private readonly int $limit,
        private readonly int $page,
        private readonly Collection $pageResults,
        private readonly ?int $totalPages = null,
        private readonly ?int $previousPage = null,
        private readonly ?int $nextPage = null)
    {}

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

	public function getTotalPages(): ?int
	{
		return $this->totalPages;
	}

	public function getPreviousPage(): ?int
	{
		return $this->previousPage;
	}

	public function getNextPage(): ?int
	{
		return $this->nextPage;
	}

    public function getPageResults(): Collection
    {
        return $this->pageResults;
    }

    public function jsonSerialize(): Collection
    {
        return $this->pageResults;
    }
}
