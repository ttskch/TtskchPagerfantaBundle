<?php

namespace App\Repository;

use App\Criteria\UserCriteria;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function createQueryBuilderFromCriteria(UserCriteria $criteria)
    {
        return $this->createQueryBuilder('u')
            ->where('u.name like :query')
            ->orWhere('u.email like :query')
            ->setParameter('query', sprintf('%%%s%%', str_replace('%', '\%', $criteria->query)))
            ->orderBy(sprintf('u.%s', $criteria->sort), $criteria->direction)
        ;
    }
}
