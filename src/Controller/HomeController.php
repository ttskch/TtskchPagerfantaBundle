<?php

namespace App\Controller;

use App\Criteria\UserCriteria;
use App\Form\UserSearchType;
use App\Repository\UserRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/", name="home_")
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index(UserRepository $userRepository)
    {
        $context = $this->get('ttskch_pagerfanta.context')->initialize('id');

        $queryBuilder = $userRepository
            ->createQueryBuilder('u')
            ->orderBy(sprintf('u.%s', $context->criteria->sort), $context->criteria->direction)
        ;

        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta
            ->setMaxPerPage($context->criteria->limit)
            ->setCurrentPage($context->criteria->page)
        ;

        return $this->render('home/index.html.twig', [
            'pagerfanta' => $pagerfanta,
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(UserRepository $userRepository)
    {
        $context = $this->get('ttskch_pagerfanta.context')->initialize('id', UserCriteria::class, UserSearchType::class);

        $queryBuilder = $userRepository->createQueryBuilderFromCriteria($context->criteria);

        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta
            ->setMaxPerPage($context->criteria->limit)
            ->setCurrentPage($context->criteria->page)
        ;

        return $this->render('home/search.html.twig', [
            'form' => $context->form->createView(),
            'pagerfanta' => $pagerfanta,
        ]);
    }

    /**
     * @Route("array", name="array")
     */
    public function array()
    {
        $context = $this->get('ttskch_pagerfanta.context')->initialize('id', UserCriteria::class, UserSearchType::class);

        $users = require __DIR__ . '/../../fixtures/users.php';

        // query
        $users = array_filter($users, function($user) use ($context) {
            return !$context->criteria->query || false !== strpos($user['name'], $context->criteria->query) || false !== strpos($user['email'], $context->criteria->query);
        });

        // sort
        array_multisort(array_column($users, $context->criteria->sort), strtolower($context->criteria->direction) === 'asc' ? SORT_ASC : SORT_DESC, $users);

        $adapter = new ArrayAdapter($users);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta
            ->setMaxPerPage($context->criteria->limit)
            ->setCurrentPage($context->criteria->page)
        ;

        return $this->render('home/search.html.twig', [
            'form' => $context->form->createView(),
            'pagerfanta' => $pagerfanta,
        ]);
    }
}
