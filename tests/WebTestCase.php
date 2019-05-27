<?php

namespace Ttskch\PagerfantaBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
