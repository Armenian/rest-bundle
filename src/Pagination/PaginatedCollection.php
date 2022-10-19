<?php

declare(strict_types=1);

namespace DMP\RestBundle\Pagination;

use Doctrine\Common\Collections\Collection;

interface PaginatedCollection
{
    public function getTotalCount(): int;
    public function getLimit(): int;
    public function getPage(): int;
    public function getPageResults(): Collection;
    public function getTotalPages(): ?int;
    public function getPreviousPage(): ?int;
    public function getNextPage(): ?int;
}
