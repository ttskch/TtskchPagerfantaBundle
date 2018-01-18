# TtskchPagerfantaBundle / Demo

![](https://user-images.githubusercontent.com/4360663/35521752-e1d22a98-055d-11e8-9b9f-b593a1eb218f.png)

## Usage

```bash
$ git clone -b demo git@github.com:ttskch/TtskchPagerfantaBundle.git demo
$ cd demo
$ cp .env{.dist,}
$ composer install
$ php -S localhost:8000 -t public
```

And browse http://localhost:8000

### Bundle specific codes

* [config/packages/ttskch_pagerfanta.yaml](config/packages/ttskch_pagerfanta.yaml)
* [src/Controller/HomeController.php](src/Controller/HomeController.php)
* [src/Criteria/UserCriteria.php](src/Criteria/UserCriteria.php)
* [src/Form/UserSearchType.php](src/Form/UserSearchType.php)
* [src/Repository/UserRepository.php](src/Repository/UserRepository.php)
* [templates/home/index.html.twig](templates/home/index.html.twig)
* [templates/home/search.html.twig](templates/home/search.html.twig)
* [translations/messages.en.yaml](translations/messages.en.yaml)
