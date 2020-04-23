<?php

namespace App\Entity\Person;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Person\PersonSettingsRepository")
 */
class PersonSettings
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shortname_expr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name_expr;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Person\PersonField", mappedBy="personSettings")
     */
    private $fields;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set id.
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getShortnameExpr(): ?string
    {
        return $this->shortname_expr;
    }

    public function setShortnameExpr(?string $shortname_expr): self
    {
        $this->shortname_expr = $shortname_expr;

        return $this;
    }

    public function getNameExpr(): ?string
    {
        return $this->name_expr;
    }

    public function setNameExpr(?string $name_expr): self
    {
        $this->name_expr = $name_expr;

        return $this;
    }

    /**
     * @return Collection|PersonField[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(PersonField $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setPersonSettings($this);
        }

        return $this;
    }

    public function removeField(PersonField $field): self
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            // set the owning side to null (unless already changed)
            if ($field->getPersonSettings() === $this) {
                $field->setPersonSettings(null);
            }
        }

        return $this;
    }
}
