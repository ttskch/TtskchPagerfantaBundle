<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Entity(repositoryClass="Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository")
 */
class User
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
