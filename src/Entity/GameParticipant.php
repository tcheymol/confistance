<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameParticipantRepository")
 */
class GameParticipant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSpy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userGames")
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUser;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $spotIndex;

    public function __toString()
    {
        return $this->getAppUser()->getUsername();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getIsSpy(): ?bool
    {
        return $this->isSpy;
    }

    public function isSpy(): ?bool
    {
        return $this->isSpy;
    }

    public function setIsSpy(?bool $isSpy): self
    {
        $this->isSpy = $isSpy;

        return $this;
    }

    public function getAppUser(): ?UserInterface
    {
        return $this->appUser;
    }

    public function setAppUser(?UserInterface $appUser): self
    {
        $this->appUser = $appUser;

        return $this;
    }

    public function getSpotIndex(): ?int
    {
        return $this->spotIndex;
    }

    public function setSpotIndex(?int $spotIndex): self
    {
        $this->spotIndex = $spotIndex;

        return $this;
    }
}
