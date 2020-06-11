<?php

namespace App\Entity\Security;

use App\Entity\Person\Person;
use App\Entity\Person\PersonField;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\UserEntityInterface;
use OpenIDConnectServer\Entities\ClaimSetInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 */
class Auth implements UserInterface, EquatableInterface, UserEntityInterface, ClaimSetInterface
{

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\Entity\Person\Person", inversedBy="auth")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $auth_id;

    /**
     * @var string The hashed password
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * Encrypted string whose value is sent to the user email address in order to (re-)set the password.
     *
     * @var string
     *
     * @ORM\Column(name="password_request_token", type="string", nullable=true)
     */
    protected $passwordRequestToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @var int
     *
     * @ ORM\Column(name="last_sign_in_at", type="datetime", nullable=true)
     */
    protected $lastSignInAt;

    /**
     * @var string
     *
     * @ ORM\Column(name="last_sign_in_ip", type="string", nullable=true)
     */
    protected $lastSignInIp;

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getPerson()->getId();
    }

    /**
     * getAuthId
     * Insert description here.
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function getAuthId(): string
    {
        return (string) $this->auth_id;
    }

    /**
     * setAuthId
     * Insert description here.
     *
     * @param string
     * @param $auth_id
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function setAuthId(string $auth_id): self
    {
        $this->auth_id = $auth_id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * Note that this value isn't loaded by doctrine, but is provided
     * by the parent Person instance.
     */
    public function getUsername(): string
    {
        return $this->getPerson()->getId();
    }

    /**
     * @Groups({"details"})
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * setPerson
     * Insert description here.
     *
     * @param string
     * @param $person
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @see UserInterface
     * @Groups({"details"})
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getClaims()
    {
        $array = [];
        if ($this->person) {
            if ($this->person->getEmail()) {
                $array['email'] = $this->person->getEmail();
                $array['email_verified'] = false;
            }
            foreach ($this->person->getKeyValues() as $kv) {
                $key = $kv['key'] instanceof PersonField ? $kv['key']->getSlug() : $kv['key'];
                $val = $kv['value']->getValue();
                $array[$key] = $val;
            }
        }
        return $array;
    }

    /**
     * @Groups({"details"})
     */
    public function isActionRequired(): bool
    {
        if (is_null($this->person->getEmail())) {
            return true;
        }

        // Check if for any field, no PersonValue is assigned
        // if so, the profile needs to be updated
        return $this->person->getKeyValues()->exists(function ($key, $x) { return is_null($x['value']); });
    }

    /**
     * setRoles
     * Insert description here.
     *
     * @param array
     * @param $roles
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * setPassword
     * Insert description here.
     *
     * @param string
     * @param $password
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Gets the last login time.
     * 
     * @Groups({"details"})
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * setLastLogin
     * Insert description here.
     *
     * @param \
     * @param DateTime
     * @param $time
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * setConfirmationToken
     * Insert description here.
     *
     * @param $passwordRequestToken
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function setPasswordRequestToken($passwordRequestToken)
    {
        $this->passwordRequestToken = $passwordRequestToken;

        return $this;
    }

    /**
     * setPasswordRequestedAt
     * Insert description here.
     *
     * @param \
     * @param DateTime
     * @param $date
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return \DateTime|null
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * isPasswordRequestNonExpired
     * Insert description here.
     *
     * @param $ttl
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return null === $this->getPasswordRequestedAt() || (
               $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time());
    }

    /**
     * isEqualTo
     * Insert description here.
     *
     * @param UserInterface
     * @param $user
     *
     * @return
     *
     * @static
     *
     * @see
     * @since
     */
    public function isEqualTo(UserInterface $user)
    {
        return $this->getUsername() === $user->getUsername();
    }

    public function getPasswordRequestToken(): ?string
    {
        return $this->passwordRequestToken;
    }

    public function getPasswordRequestSalt(): ?string
    {
        // not needed

        return null;
    }

    public function setPasswordRequestSalt(): Auth
    {
        // not needed

        return $this;
    }
}
