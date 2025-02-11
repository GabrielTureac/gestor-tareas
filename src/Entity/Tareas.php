<?php

namespace App\Entity;

use App\Repository\TareasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TareasRepository::class)]
class Tareas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
