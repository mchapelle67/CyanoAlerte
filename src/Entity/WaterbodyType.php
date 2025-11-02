<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\WaterbodyTypeRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WaterbodyTypeRepository::class)]
class WaterbodyType
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['alert:read'])]
    private ?int $id = null;

    #[Groups(['alert:read'])]
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * @var Collection<int, Waterbody>
     */
    #[ORM\OneToMany(targetEntity: Waterbody::class, mappedBy: 'type')]
    private Collection $waterbodies;

    public function __construct()
    {
        $this->waterbodies = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getType(): ?string
    {
        return $this->type;
    }
    
    public function setType(string $type): static
    {
        $this->type = $type;
        
        return $this;
    }
    
    public function __toString(): string
    {
        return $this->type;
    }

    /**
     * @return Collection<int, Waterbody>
     */
    public function getWaterbodies(): Collection
    {
        return $this->waterbodies;
    }

    public function addWaterbody(Waterbody $waterbody): static
    {
        if (!$this->waterbodies->contains($waterbody)) {
            $this->waterbodies->add($waterbody);
            $waterbody->setType($this);
        }

        return $this;
    }

    public function removeWaterbody(Waterbody $waterbody): static
    {
        if ($this->waterbodies->removeElement($waterbody)) {
            // set the owning side to null (unless already changed)
            if ($waterbody->getType() === $this) {
                $waterbody->setType(null);
            }
        }

        return $this;
    }
}
