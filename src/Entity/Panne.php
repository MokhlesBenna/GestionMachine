<?php

namespace App\Entity;

use App\Repository\PanneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanneRepository::class)]
class Panne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePanne = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $severite = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $actionsNecessaires = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateResolution = null;

    #[ORM\ManyToOne(targetEntity: Technicien::class, inversedBy: 'pannes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Technicien $technicien = null;

    #[ORM\ManyToOne(targetEntity: Machine::class, inversedBy: 'pannes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Machine $machine = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePanne(): ?\DateTimeInterface
    {
        return $this->datePanne;
    }

    public function setDatePanne(\DateTimeInterface $datePanne): static
    {
        $this->datePanne = $datePanne;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getSeverite(): ?string
    {
        return $this->severite;
    }

    public function setSeverite(string $severite): static
    {
        $this->severite = $severite;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getActionsNecessaires(): ?string
    {
        return $this->actionsNecessaires;
    }

    public function setActionsNecessaires(string $actionsNecessaires): static
    {
        $this->actionsNecessaires = $actionsNecessaires;
        return $this;
    }

    public function getDateResolution(): ?\DateTimeInterface
    {
        return $this->dateResolution;
    }

    public function setDateResolution(?\DateTimeInterface $dateResolution): static
    {
        $this->dateResolution = $dateResolution;
        return $this;
    }

    public function getTechnicien(): ?Technicien
    {
        return $this->technicien;
    }

    public function setTechnicien(?Technicien $technicien): static
    {
        $this->technicien = $technicien;
        return $this;
    }

    public function getMachine(): ?Machine
    {
        return $this->machine;
    }

    public function setMachine(?Machine $machine): static
    {
        $this->machine = $machine;
        return $this;
    }
}
