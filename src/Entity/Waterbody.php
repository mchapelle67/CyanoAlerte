<?php

namespace App\Entity;

use App\Repository\WaterbodyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WaterbodyRepository::class)]
class Waterbody
{
    #[Groups(['alert:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\Column(length: 255)]
    private ?string $latitude = null;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\Column(length: 255)]
    private ?string $longitude = null;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\Column(length: 255)]
    private ?string $department = null;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[Groups(['alert:read', 'alert:create'])]
    #[ORM\ManyToOne(inversedBy: 'waterbodies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WaterbodyType $type = null;

    /**
     * @var Collection<int, Alert>
     */
    #[ORM\OneToMany(targetEntity: Alert::class, mappedBy: 'waterbody', orphanRemoval: true)]
    private Collection $alerts;

    /**
     * @var Collection<int, Picture>
     */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'Waterbody', cascade: ['persist'], orphanRemoval: true)]
    private Collection $pictures;

    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $this->normalizeFirstUpperRestLower($name);

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): static
    {
        $this->department = $this->normalizeFirstUpperRestLower($department);

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $this->normalizeFirstUpperRestLower($city);

        return $this;
    }

    public function getType(): ?WaterbodyType
    {
        return $this->type;
    }

    public function setType(?WaterbodyType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Alert>
     */
    public function getAlerts(): Collection
    {
        return $this->alerts;
    }

    public function addAlert(Alert $alert): static
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->setWaterbody($this);
        }

        return $this;
    }

    public function removeAlert(Alert $alert): static
    {
        if ($this->alerts->removeElement($alert)) {
            // set the owning side to null (unless already changed)
            if ($alert->getWaterbody() === $this) {
                $alert->setWaterbody(null);
            }
        }

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
            $picture->setWaterbody($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getWaterbody() === $this) {
                $picture->setWaterbody(null);
            }
        }

        return $this;
    }

    private function normalizeFirstUpperRestLower(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return $value;
        }

        $value = mb_strtolower($value, 'UTF-8');

        return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($value, 1, null, 'UTF-8');
    }
}
