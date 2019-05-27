<?php

namespace Ttskch\PagerfantaBundle\Twig;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Ttskch\PagerfantaBundle\Context;
use Ttskch\PagerfantaBundle\WebTestCase;
use Twig\Environment;

class PagerfantaExtensionTest extends WebTestCase
{
    /**
     * @var PagerfantaExtension
     */
    private $SUT;

    /**
     * @var Pagerfanta
     */
    private $pagerfanta;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();

        /** @var Context $context */
        $context = $container->get('ttskch_pagerfanta.context');
        $context->initialize('id');

        /** @var Environment $twig */
        $twig = $container->get('twig');

        $adapter = new ArrayAdapter([
            ['id' => 1, 'name' => 'name1'],
            ['id' => 2, 'name' => 'name2'],
            ['id' => 3, 'name' => 'name3'],
            ['id' => 4, 'name' => 'name4'],
            ['id' => 5, 'name' => 'name5'],
        ]);

        $this->pagerfanta = new Pagerfanta($adapter);
        $this->pagerfanta->setMaxPerPage(1);
        $this->pagerfanta->setCurrentPage(1);

        $this->SUT = new PagerfantaExtension($context, $twig);
    }

    public function testRenderPager(): void
    {
        $text = $this->SUT->renderPager($this->pagerfanta);

        $expected = <<<EOT


limit
2
page
1
1
2
1
5
1
1
1
5

EOT;

        $this->assertEquals($expected, $text);
    }

    public function testRenderSortableLink(): void
    {
        $text = $this->SUT->renderSortableLink('name', 'Name');

        $expected = <<<EOT

1
sort
name
direction

asc
Name

EOT;
        $this->assertEquals($expected, $text);
    }
}
