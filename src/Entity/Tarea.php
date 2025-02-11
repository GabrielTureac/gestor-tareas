<?php
// src/Entity/Tarea.php
namespace App\Entity;

use App\Repository\TareaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TareaRepository::class)]
class Tarea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "El campo tarea no puede estar vacío.")]
    private ?string $tarea = null;

    #[ORM\Column(type: 'boolean')]
    private bool $completada = false;  // Campo completada (booleano)

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 1, max: 5, notInRangeMessage: "La prioridad debe estar entre 1 y 5.")]
    private int $prioridad = 3;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarea(): ?string
    {
        return $this->tarea;
    }

    public function setTarea(string $tarea): static
    {
        $this->tarea = $tarea;
        return $this;
    }

    // Métodos getters y setters para el campo 'completada'
    public function isCompletada(): bool
    {
        return $this->completada;
    }

    public function setCompletada(bool $completada): static
    {
        $this->completada = $completada;
        return $this;
    }

    public function getPrioridad(): int
    {
        return $this->prioridad;
    }

    public function setPrioridad(int $prioridad): static
    {
        $this->prioridad = $prioridad;
        return $this;
    }
}

