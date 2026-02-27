<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AlertRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AlertRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['alert:read']],
    denormalizationContext: ['groups' => ['alert:create']],
    formats: ['json' => 'application/json']
)]
#[GetCollection(
    security: 'is_granted("PUBLIC_ACCESS")'
)]
#[Get(
    security: 'is_granted("PUBLIC_ACCESS")'
)]
#[Post(
    security: 'is_granted("PUBLIC_ACCESS")'
)]
#[Patch(
    security: "is_granted('ROLE_ADMIN')"
)]
#[Delete(
    security: "is_granted('ROLE_ADMIN')"
)]

class Alert
{
    #[Groups(['alert:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['alert:read'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $created_at = null;

    #[Groups(['alert:read'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updated_at = null;

    #[Groups(['alert:read'])]  
    #[ORM\Column]
    private ?bool $is_verified = false;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\ManyToOne(targetEntity: Waterbody::class, inversedBy: 'alerts', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Waterbody $waterbody = null;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\ManyToOne(targetEntity: ToxicityLevel::class,inversedBy: 'alerts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ToxicityLevel $toxicity_level = null;

     #[Groups(['alert:create'])]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @var Collection<int, Picture>
     */
    #[Groups(['alert:read'])]
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'alert', cascade: ['persist'], orphanRemoval: true)]
    private Collection $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->created_at = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updated_at = new \DateTimeImmutable();

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): static
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getWaterbody(): ?Waterbody
    {
        return $this->waterbody;
    }

    public function setWaterbody(?Waterbody $waterbody): static
    {
        $this->waterbody = $waterbody;

        return $this;
    }

    public function getToxicityLevel(): ?ToxicityLevel
    {
        return $this->toxicity_level;
    }

    public function setToxicityLevel(?ToxicityLevel $toxicity_level): static
    {
        $this->toxicity_level = $toxicity_level;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setAlert($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            if ($picture->getAlert() === $this) {
                $picture->setAlert(null);
            }
        }

        return $this;
    }
}
