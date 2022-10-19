<?php

declare(strict_types=1);

namespace DMP\RestBundle\Pagination\Listener;

use DMP\RestBundle\Pagination\PaginatedCollection;
use Psr\Link\EvolvableLinkProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\WebLink\GenericLinkProvider;
use Symfony\Component\WebLink\Link;


class PaginationListener implements EventSubscriberInterface
{

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 40],
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $paginator = $event->getControllerResult();

        if (!$event->isMainRequest() || !$paginator instanceof PaginatedCollection) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        $route = $attributes->get('_route');
        $params = $attributes->get('_route_params', []);

        /** @var EvolvableLinkProviderInterface $linkProvider */
        $linkProvider = $attributes->get('_links', new GenericLinkProvider());

        foreach ($this->getPages($paginator) as $rel => $page) {
            $href = $this->urlGenerator->generate($route, $params + ['page' => $page], UrlGeneratorInterface::ABSOLUTE_URL);

            $linkProvider = $linkProvider->withLink(new Link($rel, $href));
        }

        $attributes->set('_links', $linkProvider);
        $attributes->set('_pager', $paginator);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $paginator = $event->getRequest()->attributes->get('_pager');

        if (!$paginator instanceof PaginatedCollection) {
            return;
        }

        $response = $event->getResponse();

        $response->headers->set('X-Page', (string)$paginator->getPage());
        $response->headers->set('X-Limit', (string)$paginator->getLimit());
        $response->headers->set('X-Total-Count', (string)$paginator->getTotalCount());
        $response->headers->set('X-Count', (string)$paginator->getPageResults()->count());
    }

    private function getPages(PaginatedCollection $paginator): array
    {
        $page = $paginator->getPage();
        $last = $paginator->getTotalPages() ? $paginator->getTotalPages() : 1;

        $pages = ['first' => 1, 'last' => $last];

        if ($page > 1) {
            $pages['prev'] = $page - 1;
        }
        if ($page < $last) {
            $pages['next'] = $page + 1;
        }

        return $pages;
    }
}
