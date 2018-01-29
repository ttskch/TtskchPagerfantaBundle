<?php

namespace Ttskch\PagerfantaBundle\Twig;

use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Ttskch\PagerfantaBundle\Config;
use Twig\Environment;
use Twig\Extension\AbstractExtension;

class PagerfantaExtension extends AbstractExtension
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Config $config, UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, Environment $twig)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->request = $requestStack->getCurrentRequest();
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ttskch_pagerfanta_pager', [$this, 'renderPager'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('ttskch_pagerfanta_sortable', [$this, 'renderSortableLink'], ['is_safe' => ['html']]),
        ];
    }

    public function renderPager(Pagerfanta $pagerfanta, $templateName = null, array $context = [])
    {
        $templateName = $templateName ?: $this->config->templatePager;

        $currentPage = $pagerfanta->getCurrentPage();
        $firstPage = 1;
        $lastPage = $pagerfanta->getNbPages();
        $leftPage = max($currentPage - (intval(floor(($this->config->pageRange - 1) / 2))), $firstPage);
        $rightPage = min($leftPage + $this->config->pageRange - 1, $lastPage);
        if ($rightPage === $lastPage) {
            $leftPage = max($rightPage - $this->config->pageRange + 1, $firstPage);
        }

        $context = array_merge($context, [
            'route' => $this->request->get('_route'),
            'queries' => $this->request->query->all(),
            'limit_name' => $this->config->limitName,
            'limit_current' => $this->request->get($this->config->limitName) ?: $this->config->limitDefault,
            'page_name' => $this->config->pageName,
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

    public function renderSortableLink($key, $defaultKey = null, $text = null, $templateName = null, array $context = [])
    {
        $templateName = $templateName ?: $this->config->templateSortable;

        $isSorted = (!$this->request->get($this->config->sortKeyName) && $key === $defaultKey) || $this->request->get($this->config->sortKeyName) === $key;

        $currentDirection = $isSorted ? ($this->request->get($this->config->sortDirectionName) ?: $this->config->sortDirectionDefault) : null;
        $nextDirection = $isSorted ? (strtolower($currentDirection) === 'asc' ? 'desc' : 'asc') : $this->config->sortDirectionDefault;

        // reset page number after re-sorting.
        $queries = $this->request->query->all();
        unset($queries[$this->config->pageName]);

        $context = array_merge($context, [
            'route' => $this->request->get('_route'),
            'queries' => $queries,
            'key_name' => $this->config->sortKeyName,
            'key' => $key,
            'direction_name' => $this->config->sortDirectionName,
            'direction_current' => $currentDirection,
            'direction_next' => $nextDirection,
            'text' => $text ?: ucwords($key),
        ]);

        return $this->twig->render($templateName, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ttskch_pagerfanta';
    }
}
