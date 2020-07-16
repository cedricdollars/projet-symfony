<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $FirstName;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $LastName;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $NumEtud;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="etudiants")
     */
    private $departement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): self
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(string $LastName): self
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function getNumEtud(): ?int
    {
        return $this->NumEtud;
    }

    public function setNumEtud(int $NumEtud): self
    {
        $this->NumEtud = $NumEtud;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'FirstName' => $this->getFirstName(),
            'LastName' => $this->getLastName(),
            'NumEtud' => $this->getNumEtud()
        ];
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }
}
