# TtskchPagerfantaBundle

[![Latest Stable Version](https://poser.pugx.org/ttskch/pagerfanta-bundle/version)](https://packagist.org/packages/ttskch/pagerfanta-bundle)
[![Total Downloads](https://poser.pugx.org/ttskch/pagerfanta-bundle/downloads)](https://packagist.org/packages/ttskch/pagerfanta-bundle)

Most easy and customizable way to use [Pagerfanta](https://github.com/whiteoctober/Pagerfanta) with Symfony.

## Features

Advantages compared to [WhiteOctoberPagerfantaBundle](https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle):

* So **light weight**
* Customizable **twig-templated views**
* **Sortable link** feature
* Easy to use with **search form**

## Demo

You can easily try demo app like below on [demo branch](https://github.com/ttskch/TtskchPagerfantaBundle/tree/demo).

![](https://user-images.githubusercontent.com/4360663/35103820-61598eb2-fcaa-11e7-9622-7d16c1b242a6.png)

## Requirement

* Symfony ^3.4|^4.0

## Installation

```bash
$ composer require ttskch/pagerfanta-bundle:@dev
```

```php
// config/bundles.php
return [
    // ...
    Ttskch\PagerfantaBundle\TtskchPagerfantaBundle:class => ['all' => true],
];
```

## Usage

```php
// FooController.php

public function index(Request $request)
{
    $config = $this->get('ttskch_pagerfanta.config');

    $queryBuilder = ...
    
    $adapter = new DoctrineORMAdapter($queryBuilder);
    $pagerfanta = new Pagerfanta($adapter);
    $pagerfanta
        ->setMaxPerPage($request->get($config->limitName, $config->limitDefault))
        ->setCurrentPage($request->get($config->pageName, 1))
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

See [Twig/PagerfantaExtension.php](Twig/PagerfantaExtension.php) to learn more about twig functions.

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

use Ttskch\PagerfantaBundle\Form\PaginationCriteria;

class FooCriteria extends PaginationCriteria
{
    public $query;
}
```

```php
// FooType.php

use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ttskch\PagerfantaBundle\Form\PaginationType;

class FooSearchType extends PaginationType
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
    $qb = $this->createQueryBuilder('f');

    if ($query = $criteria->query) {
        $qb
            ->where('f.name like :query')
            ->orWhere('f.email like :query')
            ->setParameter('query', sprintf('%%%s%%', str_replace('%', '\%', $query)))
        ;
    }

    if ($sort = $criteria->sort) {
        $qb
            ->orderBy(sprintf('f.%s', $sort), $criteria->direction)
        ;
    }

    return $qb;
}
```

```php
// FooController.php

public function index(Request $request, FormFactoryInterface $formFactory, FooRepository $repository)
{
    $config = $this->get('ttskch_pagerfanta.config');

    $criteria = new FooCriteria($config->limitDefault, 'id' /* default sort key*/, $config->sortDirectionDefault);

    $searchForm = $formFactory->createNamed('', FooSearchType::class, $criteria, [
        'method' => 'GET',
    ]);
    // don't use handleRequest because missing queries will clear corresponding fields of criteria.
    $searchForm->submit($request->query->all(), false);

    $queryBuilder = $repository->createQueryBuilderFromCriteria($criteria);
    
    $adapter = new DoctrineORMAdapter($queryBuilder);
    $pagerfanta = new Pagerfanta($adapter);
    $pagerfanta
        ->setMaxPerPage($criteria->limit)
        ->setCurrentPage($criteria->page)
    ;

    // prevent rendering page field to reset page to 1 after searching.
    $formView = $searchForm->createView();
    $formView->children[$config->pageName]->setRendered();
    
    return $this->render('index.html.twig', [
        'form' => $formView,
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
