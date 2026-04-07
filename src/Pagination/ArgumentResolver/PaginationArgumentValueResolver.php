<?php

declare(strict_types=1);

namespace DMP\RestBundle\Pagination\ArgumentResolver;


use DMP\RestBundle\Pagination\ArgumentResolver\Exception\PaginationArgumentValueResolverTypeNotSupporting;
use DMP\RestBundle\Pagination\Pagination;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class PaginationArgumentValueResolver implements ValueResolverInterface
{
    public function __construct(
        private int $limit,
        private int $maxLimit,
        private string $type)
    {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (Pagination::class !== $argument->getType()) {
            return [];
        }

        $resolver = sprintf('resolve%s', ucfirst($this->type));
        if (!method_exists($this, $resolver)) {
            throw new PaginationArgumentValueResolverTypeNotSupporting($this->type);
        }

        return $this->$resolver($request, $argument);
    }

    public function resolveQuery(Request $request, ArgumentMetadata $argument): iterable
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', $this->limit);

        $page = $page > 0 ? $page : 1;
        $limit = min($limit > 0 ? $limit : 1, $this->maxLimit);

        yield new Pagination($page, $limit);
    }

    public function resolveHeader(Request $request, ArgumentMetadata $argument): iterable
    {
        $page = (int)$request->headers->get('X-Page', "1");
        $limit = (int)$request->headers->get('X-Page-Limit', (string)$this->limit);

        $page = $page > 0 ? $page : 1;
        $limit = min($limit > 0 ? $limit : 1, $this->maxLimit);

        yield new Pagination($page, $limit);
    }
}
