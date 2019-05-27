# TtskchPagerfantaBundle

[![Travis (.com)](https://img.shields.io/travis/com/ttskch/TtskchPagerfantaBundle.svg?style=flat-square)](https://travis-ci.com/ttskch/TtskchPagerfantaBundle)
[![Latest Stable Version](https://poser.pugx.org/ttskch/pagerfanta-bundle/version?format=flat-square)](https://packagist.org/packages/ttskch/pagerfanta-bundle)
[![Total Downloads](https://poser.pugx.org/ttskch/pagerfanta-bundle/downloads?format=flat-square)](https://packagist.org/packages/ttskch/pagerfanta-bundle)

Most easy and customizable way to use [Pagerfanta](https://github.com/whiteoctober/Pagerfanta) with Symfony.

## Features

Advantages compared to [WhiteOctoberPagerfantaBundle](https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle):

* So **light weight**
* Customizable **twig-templated views**
* **Sortable link** feature
* Easy to use with **search form**
* Preset **bootstrap4 theme**

## Demo

You can easily try demo app like below on [demo branch](https://github.com/ttskch/TtskchPagerfantaBundle/tree/demo).

![](https://user-images.githubusercontent.com/4360663/35521752-e1d22a98-055d-11e8-9b9f-b593a1eb218f.png)

## Requirement

* PHP ^7.1.3
* Symfony ^4.0

## Installation

```bash
$ composer require ttskch/pagerfanta-bundle
```

```php
// config/bundles.php

return [
    // ...
    Ttskch\PagerfantaBundle\TtskchPagerfantaBundle::class => ['all' => true],
];
```

## Usage

```yaml
# services.yaml

services:
    Ttskch\PagerfantaBundle\Context: "@ttskch_pagerfanta.context"
```

```php
// FooController.php

public function index(FooRepository $fooRepository, Context $context)
{
    $context->initialize('id');

    $queryBuilder = $fooRepository
        ->createQueryBuilder('f')
        ->orderBy(sprintf('f.%s', $context->criteria->sort), $context->criteria->direction)
    ;

    $adapter = new DoctrineORMAdapter($queryBuilder);
    $pagerfanta = new Pagerfanta($adapter);
    $pagerfanta
        ->setMaxPerPage($context->criteria->limit)
        ->setCurrentPage($context->criteria->page)
    ;

    return $this->render('index.html.twig', [
        'pagerfanta' => $pagerfanta,
    ]);
}
```

```twig
{# index.html.twig #}

{% set keys = ['id', 'name', 'email'] %}
<table>
    <thead>
    <tr>
        {% for key in keys %}
            <th>{{ ttskch_pagerfanta_sortable(key) }}</th>
        {% endfor %}
    </tr>
    </thead>
    <tbody>
    {% for item in pagerfanta.getCurrentPageResults() %}
        <tr>
            {% for key in keys %}
                <td>{{ attribute(item, key) }}</td>
            {% endfor %}
        </tr>
    {% endfor %}
    </tbody>
</table>

{{ ttskch_pagerfanta_pager(pagerfanta) }}
```

See [src/Twig/PagerfantaExtension.php](src/Twig/PagerfantaExtension.php) to learn more about twig functions.

### Configuring

```bash
$ bin/console config:dump-reference ttskch_pagerfanta
# Default configuration for extension with alias: "ttskch_pagerfanta"
ttskch_pagerfanta:
    page:
        name:                 page
        range:                5
    limit:
        name:                 limit
        default:              10
    sort:
        key:
            name:                 sort
        direction:
            name:                 direction

            # "asc" or "desc"
            default:              asc
    template:
        pager:                '@TtskchPagerfanta/pager/default.html.twig'
        sortable:             '@TtskchPagerfanta/sortable/default.html.twig'
```

### Customizing views

#### Use preset bootstrap4 theme

Just configure bundle like below.

```yaml
# config/packages/ttskch_pagerfanta.yaml

ttskch_pagerfanta:
    template:
        pager: '@TtskchPagerfanta/pager/bootstrap4.html.twig'
```

#### Use your own theme

Create your own templates and configure bundle like below.

```yaml
# config/packages/ttskch_pagerfanta.yaml

ttskch_pagerfanta:
    template:
        pager: 'your/own/pager.html.twig'
        sortable: 'your/own/sortable.html.twig'
```

### Using with search form

```php
// FooCriteria.php

use Ttskch\PagerfantaBundle\Entity\Criteria;

class FooCriteria extends Criteria
{
    public $query;
}
```

```php
// FooSearchType.php

use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ttskch\PagerfantaBundle\Form\CriteriaType;

class FooSearchType extends CriteriaType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('query', SearchType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FooCriteria::class,
        ]);
    }
}
```

```php
// FooRepository.php

public function createQueryBuilderFromCriteria(FooCriteria $criteria)
{
    return $this->createQueryBuilder('f')
        ->where('f.name like :query')
        ->orWhere('f.email like :query')
        ->setParameter('query', sprintf('%%%s%%', str_replace('%', '\%', $criteria->query)))
        ->orderBy(sprintf('f.%s', $criteria->sort), $criteria->direction)
    ;
}
```

```php
// FooController.php

public function index(FooRepository $fooRepository)
{
    $context = $this->get('ttskch_pagerfanta.context')->initialize('id', FooCriteria::class, FooSearchType::class);

    $queryBuilder = $fooRepository->createQueryBuilderFromCriteria($context->criteria);

    $adapter = new DoctrineORMAdapter($queryBuilder);
    $pagerfanta = new Pagerfanta($adapter);
    $pagerfanta
        ->setMaxPerPage($context->criteria->limit)
        ->setCurrentPage($context->criteria->page)
    ;

    return $this->render('index.html.twig', [
        'form' => $context->form->createView(),
        'pagerfanta' => $pagerfanta,
    ]);
}
```

```twig
{# index.html.twig #}

{{ form(form, {action: path('index'), method: 'get'}) }}

{% set keys = ['id', 'name', 'email'] %}
<table>
    <thead>
    <tr>
        {% for key in keys %}
            <th>{{ ttskch_pagerfanta_sortable(key) }}</th>
        {% endfor %}
    </tr>
    </thead>
    <tbody>
    {% for item in pagerfanta.getCurrentPageResults() %}
        <tr>
            {% for key in keys %}
                <td>{{ attribute(item, key) }}</td>
            {% endfor %}
        </tr>
    {% endfor %}
    </tbody>
</table>

{{ ttskch_pagerfanta_pager(pagerfanta) }}
```
