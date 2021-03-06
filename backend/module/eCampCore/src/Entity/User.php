<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * @ORM\Entity(repositoryClass="eCamp\Core\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends AbstractCampOwner implements RoleInterface {
    const STATE_NONREGISTERED 	= "non-registered";
    const STATE_REGISTERED 		= "registered";
    const STATE_ACTIVATED  		= "activated";
    const STATE_DELETED			= "deleted";

    const ROLE_GUEST			= "guest";
    const ROLE_USER				= "user";
    const ROLE_ADMIN			= "admin";

    public function __construct() {
        parent::__construct();

        $this->state = self::STATE_NONREGISTERED;
        $this->role = self::ROLE_GUEST;

        $this->memberships = new ArrayCollection();
        $this->collaborations = new ArrayCollection();
        $this->userIdentities = new ArrayCollection();
    }


    /**
     * Unique username, lower alphanumeric symbols and underscores only
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true, unique=true )
     */
    private $username;

    /**
     * @var MailAddress
     * @ORM\OneToOne(targetEntity="MailAddress", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="trusted_mailaddress_id", referencedColumnName="id")
     */
    private $trustedMailAddress;

    /**
     * @var MailAddress
     * @ORM\OneToOne(targetEntity="MailAddress", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="untrusted_mailaddress_id", referencedColumnName="id")
     */
    private $untrustedMailAddress;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $state;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $role;

    /**
     * @var Login
     * @ORM\OneToOne(targetEntity="Login", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private $login;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="UserIdentity", mappedBy="user", orphanRemoval=true)
     */
    protected $userIdentities;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="GroupMembership", mappedBy="user", orphanRemoval=true)
     */
    protected $memberships;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="user", orphanRemoval=true)
     */
    protected $collaborations;


    /**
     * @return string
     */
    public function getRoleId() {
        return $this->role ?: self::ROLE_GUEST;
    }


    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }


    /**
     * @return string
     */
    public function getDisplayName() {
        return $this->username;
    }


    /**
     * @return string
     */
    public function getTrustedMailAddress(): string {
        if ($this->trustedMailAddress === null) {
            return "";
        }
        return $this->trustedMailAddress->getMail();
    }

    public function setTrustedMailAddress(string $mail) {
        if ($this->trustedMailAddress == null) {
            $this->trustedMailAddress = new MailAddress();
        }
        $this->untrustedMailAddress = null;
        $this->trustedMailAddress->setMail($mail);
    }

    /**
     * @return string
     */
    public function getUntrustedMailAddress(): string {
        if ($this->untrustedMailAddress === null) {
            return "";
        }
        return $this->untrustedMailAddress->getMail();
    }

    /**
     * @param string $mailAddress
     * @return string
     */
    public function setMailAddress(string $mailAddress): string {
        if ($this->getTrustedMailAddress() !== $mailAddress) {
            if ($this->untrustedMailAddress === null) {
                $this->untrustedMailAddress = new MailAddress();
            }
            $this->untrustedMailAddress->setMail($mailAddress);
            return $this->untrustedMailAddress->createVerificationCode();
        } else {
            $this->untrustedMailAddress = null;
            return "";
        }
    }

    /**
     * @param string $hash
     * @return bool
     * @throws \Exception
     */
    public function verifyMailAddress(string $hash): bool {
        if ($this->state === self::STATE_NONREGISTERED) {
            throw new \Exception("Verification failed.");
        }
        if ($this->state === self::STATE_DELETED) {
            throw new \Exception("Verification failed.");
        }

        if ($this->untrustedMailAddress === null) {
            throw new \Exception("Verification failed.");
        }

        $verified = $this->untrustedMailAddress->verify($hash);

        if ($verified) {
            if ($this->state === self::STATE_REGISTERED) {
                $this->state = self::STATE_ACTIVATED;
            }

            $this->trustedMailAddress = $this->untrustedMailAddress;
            $this->untrustedMailAddress = null;
        }

        return $verified;
    }


    /**
     * @return string
     */
    public function getState(): string {
        return $this->state;
    }

    public function setState(string $state): void {
        $this->state = $state;
    }


    /**
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }


    /**
     * @return Login
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * @return ArrayCollection
     */
    public function getGroupMemberships(): ArrayCollection {
        return $this->memberships;
    }

    public function addGroupMembership(GroupMembership $membership) {
        $membership->setUser($this);
        $this->memberships->add($membership);
    }

    public function removeGroupMembership(GroupMembership $membership) {
        $membership->setUser(null);
        $this->memberships->removeElement($membership);
    }


    /**
     * @return ArrayCollection
     */
    public function getCampCollaborations(): ArrayCollection {
        return $this->collaborations;
    }

    public function addCampCollaboration(CampCollaboration $collaboration) {
        $collaboration->setUser($this);
        $this->collaborations->add($collaboration);
    }

    public function removeCampCollaboration(CampCollaboration $collaboration) {
        $collaboration->setUser(null);
        $this->collaborations->removeElement($collaboration);
    }

    /**
     * @return ArrayCollection
     */
    public function getUserIdentities(): ArrayCollection {
        return $this->userIdentities;
    }

    public function addUserIdentity(UserIdentity $userIdentity) {
        $userIdentity->setUser($this);
        $this->userIdentities->add($userIdentity);
    }

    public function removeUserIdentity(UserIdentity $userIdentity) {
        $userIdentity->setUser(null);
        $this->userIdentites->removeElement($userIdentity);
    }
}
