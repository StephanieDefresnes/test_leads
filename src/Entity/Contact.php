<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $idContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $civ = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $tel = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addresse1 = null;

    #[ORM\Column(length: 255)]
    private ?string $cp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $modele = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeConcession = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $echeanceProjet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $campagne = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmitted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdContact(): ?string
    {
        return $this->idContact;
    }

    public function setIdContact(string $idContact): self
    {
        $this->idContact = $idContact;

        return $this;
    }

    public function getCiv(): ?string
    {
        return $this->civ;
    }

    public function setCiv(?string $civ): self
    {
        $this->civ = $civ;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddresse1(): ?string
    {
        return $this->addresse1;
    }

    public function setAddresse1(?string $addresse1): self
    {
        $this->addresse1 = $addresse1;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): self
    {
        $this->modele = $modele;

        return $this;
    }

    public function getCodeConcession(): ?string
    {
        return $this->codeConcession;
    }

    public function setCodeConcession(?string $codeConcession): self
    {
        $this->codeConcession = $codeConcession;

        return $this;
    }

    public function getEcheanceProjet(): ?string
    {
        return $this->echeanceProjet;
    }

    public function setEcheanceProjet(?string $echeanceProjet): self
    {
        $this->echeanceProjet = $echeanceProjet;

        return $this;
    }

    public function getCampagne(): ?string
    {
        return $this->campagne;
    }

    public function setCampagne(?string $campagne): self
    {
        $this->campagne = $campagne;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function isTransmitted(): ?bool
    {
        return $this->transmitted;
    }

    public function setTransmitted(?bool $transmitted): self
    {
        $this->transmitted = $transmitted;

        return $this;
    }
}
