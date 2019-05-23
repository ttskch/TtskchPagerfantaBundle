<?php

namespace Ttskch\PagerfantaBundle\Twig;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Ttskch\PagerfantaBundle\Context;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagerfantaExtension extends AbstractExtension
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Context $context, UrlGeneratorInterface $urlGenerator, Environment $twig)
    {
        $this->context = $context;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('ttskch_pagerfanta_pager', [$this, 'renderPager'], ['is_safe' => ['html']]),
            new TwigFunction('ttskch_pagerfanta_sortable', [$this, 'renderSortableLink'], ['is_safe' => ['html']]),
        ];
    }

    public function renderPager(Pagerfanta $pagerfanta, string $templateName = null, array $context = []): string
    {
        $templateName = $templateName ?: $this->context->config->templatePager;

        $currentPage = $pagerfanta->getCurrentPage();
        $firstPage = 1;
        $lastPage = $pagerfanta->getNbPages();
        $leftPage = max($currentPage - (intval(floor(($this->context->config->pageRange - 1) / 2))), $firstPage);
        $rightPage = min($leftPage + $this->context->config->pageRange - 1, $lastPage);
        if ($rightPage === $lastPage) {
            $leftPage = max($rightPage - $this->context->config->pageRange + 1, $firstPage);
        }

        $context = array_merge($context, [
            'route' => $this->context->request->get('_route'),
            'queries' => $this->context->request->query->all(),
            'limit_name' => $this->context->config->limitName,
            'limit_current' => $this->context->criteria->limit,
            'page_name' => $this->context->config->pageName,
            'page_current' => $currentPage,
            'page_left' => $leftPage,
            'page_right' => $rightPage,
            'page_first' => $firstPage,
            'page_last' => $lastPage,
            'item_left' => $pagerfanta->getCurrentPageOffsetStart(),
            'item_right' => $pagerfanta->getCurrentPageOffsetEnd(),
            'item_first' => 1,
            'item_last' => $pagerfanta->count(),
        ]);

        return $this->twig->render($templateName, $context);
    }

    public function renderSortableLink(string $key, string $text = null, string $templateName = null, array $context = []): string
    {
        $templateName = $templateName ?: $this->context->config->templateSortable;

        $isSorted = $key === $this->context->criteria->sort;

        $currentDirection = $isSorted ? $this->context->criteria->direction : null;
        $nextDirection = $isSorted ? (strtolower($currentDirection) === 'asc' ? 'desc' : 'asc') : $this->context->config->sortDirectionDefault;

        // reset page number after re-sorting.
        $queries = $this->context->request->query->all();
        $queries[$this->context->config->pageName] = 1;

        $context = array_merge($context, [
            'route' => $this->context->request->get('_route'),
            'queries' => $queries,
            'key_name' => $this->context->config->sortKeyName,
            'key' => $key,
            'direction_name' => $this->context->config->sortDirectionName,
            'direction_current' => $currentDirection,
            'direction_next' => $nextDirection,
            'text' => $text ?: ucwords($key),
        ]);

        return $this->twig->render($templateName, $context);
    }

    public function getName(): string
    {
        return 'ttskch_pagerfanta';
    }
}
