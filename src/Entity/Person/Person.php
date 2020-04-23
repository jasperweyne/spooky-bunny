<?php

namespace App\Entity\Person;

use App\Entity\Security\Auth;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 */
class Person
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Security\Auth")
     */
    //private $client;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Security\Auth", mappedBy="person")
     */
    private $auth;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    public function __construct()
    {
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

    /**
     * Get authentication entity.
     *
     * @return string
     */
    public function getAuth(): ?Auth
    {
        return $this->auth;
    }

    /**
     * Set authentication entity.
     *
     * @param Auth $auth
     */
    public function setAuth(?Auth $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get authentication entity.
     *
     * @return string
     */
    public function getClient(): ?Auth
    {
        return $this->client;
    }

    /**
     * Set authentication entity, which is an OAuth2.0 client entity.
     * This class is included in the OAuth bundle used in bunny.
     *
     * @param Auth $client
     */
    public function setClient(?Auth $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get email address.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email address.
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
