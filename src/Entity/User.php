<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="app_user")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $username = '';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="creator")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SquadMember", mappedBy="member")
     */
    private $squadMembers;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->squadMembers = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

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

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setCreator($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getCreator() === $this) {
                $game->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SquadMember[]
     */
    public function getSquadMembers(): Collection
    {
        return $this->squadMembers;
    }

    public function addSquadMember(SquadMember $squadMember): self
    {
        if (!$this->squadMembers->contains($squadMember)) {
            $this->squadMembers[] = $squadMember;
            $squadMember->setMember($this);
        }

        return $this;
    }

    public function removeSquadMember(SquadMember $squadMember): self
    {
        if ($this->squadMembers->contains($squadMember)) {
            $this->squadMembers->removeElement($squadMember);
            // set the owning side to null (unless already changed)
            if ($squadMember->getMember() === $this) {
                $squadMember->setMember(null);
            }
        }

        return $this;
    }
}
