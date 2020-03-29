<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_rand;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SquadRepository")
 */
class Squad
{
    const STATUS_CREATED = 'STATUS_CREATED';
    const STATUS_PLAYING = 'STATUS_PLAYING';
    const STATUS_PLAYED = 'STATUS_PLAYED';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="squads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SquadMember", mappedBy="squad", cascade={"remove"})
     */
    private $squadMembers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $spotsCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $squadIndex;

    public function __construct()
    {
        $this->squadMembers = new ArrayCollection();
        $this->status = self::STATUS_CREATED;
    }

    public function __toString()
    {
        return (string) $this->id;
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
            $squadMember->setSquad($this);
        }

        return $this;
    }

    public function removeSquadMember(SquadMember $squadMember): self
    {
        if ($this->squadMembers->contains($squadMember)) {
            $this->squadMembers->removeElement($squadMember);
            // set the owning side to null (unless already changed)
            if ($squadMember->getSquad() === $this) {
                $squadMember->setSquad(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSpotsCount(): ?int
    {
        return $this->spotsCount;
    }

    public function setSpotsCount(?int $spotsCount): self
    {
        $this->spotsCount = $spotsCount;

        return $this;
    }

    public function isParticipantASquadMember(GameParticipant $participant): bool
    {
        $isParticipant = false;

        foreach ($this->getSquadMembers() as $member) {
            if ($member->getMember() - $this->getId() === $participant->getAppUser()->getId()) {
                $isParticipant = false;
            }
        }

        return $isParticipant;
    }

    public function getSquadMembersId(): array
    {
        $membersIds = [];
        foreach ($this->getSquadMembers() as $member) {
            if ($member->getMember() !== null) {
                $membersIds[] = $member->getMember()->getId();
            }
        }

        return $membersIds;
    }

    public function getSquadMembersUsers(): array
    {
        $membersIds = [];
        foreach ($this->getSquadMembers() as $member) {
            if ($member->getMember() !== null) {
                $membersIds[] = $member->getMember();
            }
        }

        return $membersIds;
    }

    public function getUserSquadMember(User $user): ?SquadMember
    {
        foreach ($this->getSquadMembers() as $members) {
            if ($members->getMember() === $user) {
                return $members;
            }
        }

        return null;
    }

    public function isPlayed(): bool
    {
        return $this->status === self::STATUS_PLAYED;
    }

    public function isSuccess(): bool
    {
        $isSuccess = true;
        foreach ($this->squadMembers as $squadMember) {
            if ($squadMember !== null && $squadMember->getSpyPlayed() === true) {
                $isSuccess = false;
            }
        }

        return $isSuccess;
    }

    public function getSquadIndex(): ?int
    {
        return $this->squadIndex;
    }

    public function setSquadIndex(?int $squadIndex): self
    {
        $this->squadIndex = $squadIndex;

        return $this;
    }


    public function getResults(): array
    {
        $randomizedResults = [];
        $orderedResults = $this->getSquadMembers()->map(function (SquadMember $member) {
            return $member->getSpyPlayed();
        });
        $resultsCount = $orderedResults->count();

        for ($i = 0; $i < $resultsCount; $i++) {
            $randomIndex = array_rand($orderedResults->toArray());
            $randomizedResults[] = $orderedResults[$randomIndex];
            unset($orderedResults[$randomIndex]);
        }

        return $randomizedResults;
    }


}
