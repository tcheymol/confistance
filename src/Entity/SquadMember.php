<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SquadMemberRepository")
 */
class SquadMember
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Squad", inversedBy="squadMembers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $squad;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="squadMembers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $member;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $spyPlayed;

    public function __toString()
    {
        return $this->getMember() === null ? '' : $this->getMember()->getUsername();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSquad(): ?Squad
    {
        return $this->squad;
    }

    public function setSquad(?Squad $squad): self
    {
        $this->squad = $squad;

        return $this;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getSpyPlayed(): ?bool
    {
        return $this->spyPlayed;
    }

    public function setSpyPlayed(?bool $spyPlayed): self
    {
        $this->spyPlayed = $spyPlayed;

        return $this;
    }
}
