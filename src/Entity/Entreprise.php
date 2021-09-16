<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EntrepriseRepository::class)
 */
class Entreprise
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=75)
     */
    private $idEntreprise;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $ville;

    /**
     * @ORM\OneToOne(targetEntity=Client::class, inversedBy="entreprise", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="telephoneEntreprise", referencedColumnName="telephone", nullable=false)
     */
    private $telephoneEntreprise;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $telephoneResponsable;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct(){
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getIdEntreprise(): ?string
    {
        return $this->idEntreprise;
    }

    public function setIdEntreprise(string $idEntreprise): self
    {
        $this->idEntreprise = $idEntreprise;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTelephoneEntreprise(): ?Client
    {
        return $this->telephoneEntreprise;
    }

    public function setTelephoneEntreprise(Client $telephoneEntreprise): self
    {
        $this->telephoneEntreprise = $telephoneEntreprise;

        return $this;
    }

    public function getTelephoneResponsable(): ?string
    {
        return $this->telephoneResponsable;
    }

    public function setTelephoneResponsable(string $telephoneResponsable): self
    {
        $this->telephoneResponsable = $telephoneResponsable;

        return $this;
    }
}
