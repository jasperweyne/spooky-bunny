<?php

declare(strict_types=1);

namespace App\Entity\Security;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NonceRepository")
 */
class Nonce
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
    private $requestId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codeId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $string;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiry;

    /*public function __construct(
        DateTimeInterface $expiry,
        string $requestId,
        string $nonce)
    {
        $this->expiry = $expiry;
        $this->requestId = $requestId;
        $this->string = $nonce;
    }*/

    /**
     * Set Id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get Id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nonce payload.
     *
     * @param string $string
     */
    public function setString($string)
    {
        $this->string = $string;
    }

    /**
     * Get nonce payload.
     *
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set associated code_id.
     *
     * @param string $string
     */
    public function setCodeId($id)
    {
        $this->codeId = $id;
    }

    /**
     * Get associated code_id.
     *
     * @return string
     */
    public function getCodeId()
    {
        return $this->codeId;
    }

    /**
     * Set associated request_id.
     *
     * @param string $string
     */
    public function setRequestId($id)
    {
        $this->requestId = $id;
    }

    /**
     * Get associated request_id.
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    public function getExpiryDateTime(): DateTimeInterface
    {
        return $this->expiry;
    }

    public function setExpiryDateTime(DateTimeInterface $time)
    {
        $this->expiry = $time;
    }
}
