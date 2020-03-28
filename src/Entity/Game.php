<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_filter;
use function array_key_exists;
use function array_pop;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    const NUMBER_OF_SPY_MAPPING = [
        5 => 2,
        6 => 2,
        7 => 3,
        8 => 3,
        9 => 3,
        10 => 4,
    ];

    const SQUADS_MAPPING = [
        5 => [2, 3, 2, 3, 3],
        6 => [2, 3, 4, 3, 4],
        7 => [2, 3, 3, 4, 4],
        8 => [3, 4, 4, 5, 5],
        9 => [3, 4, 4, 5, 5],
        10 => [3, 4, 4, 5, 5],
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOver;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Squad", mappedBy="game", cascade={"remove"})
     */
    private $squads;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameParticipant", mappedBy="game", cascade={"remove"})
     */
    private $participants;

    /**
     * @var integer
     */
    private $numberOfParticipants;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $indexNextPlayer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $indexNextSquad;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isStarted;

    public function __construct()
    {
        $this->isStarted = false;
        $this->indexNextSquad = 0;
        $this->squads = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsOver(): ?bool
    {
        return $this->isOver;
    }

    public function setIsOver(bool $isOver): self
    {
        $this->isOver = $isOver;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection|Squad[]
     */
    public function getSquads(): Collection
    {
        return $this->squads;
    }

    public function addSquad(Squad $squad): self
    {
        if (!$this->squads->contains($squad)) {
            $this->squads[] = $squad;
            $squad->setGame($this);
        }

        return $this;
    }

    public function removeSquad(Squad $squad): self
    {
        if ($this->squads->contains($squad)) {
            $this->squads->removeElement($squad);
            // set the owning side to null (unless already changed)
            if ($squad->getGame() === $this) {
                $squad->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GameParticipant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(GameParticipant $userGame): self
    {
        if (!$this->participants->contains($userGame)) {
            $this->participants[] = $userGame;
            $userGame->setGame($this);
        }

        return $this;
    }

    public function removeParticipant(GameParticipant $userGame): self
    {
        if ($this->participants->contains($userGame)) {
            $this->participants->removeElement($userGame);
            // set the owning side to null (unless already changed)
            if ($userGame->getGame() === $this) {
                $userGame->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfParticipants(): ?int
    {
        return $this->numberOfParticipants;
    }

    public function getSquadsDistribution(): ?array
    {
        if ($this->numberOfParticipants && array_key_exists($this->numberOfParticipants, self::SQUADS_MAPPING)) {
            return self::SQUADS_MAPPING[$this->numberOfParticipants];
        }

        return null;
    }

    /**
     * @param int $numberOfParticipants
     */
    public function setNumberOfParticipants(int $numberOfParticipants = 0): void
    {
        $this->numberOfParticipants = $numberOfParticipants;
    }

    public function getSpiesCount(): ?int
    {
        if ($this->numberOfParticipants && array_key_exists($this->numberOfParticipants, self::NUMBER_OF_SPY_MAPPING)) {
            return self::NUMBER_OF_SPY_MAPPING[$this->numberOfParticipants];
        }

        return null;
    }

    public function getIndexNextPlayer(): ?int
    {
        return $this->indexNextPlayer;
    }

    public function setIndexNextPlayer(?int $indexNextPlayer): self
    {
        $this->indexNextPlayer = $indexNextPlayer;

        return $this;
    }

    public function getCurrentSquad(): ?Squad
    {
        $currentSquads = array_filter($this->squads->toArray(), function (Squad $squad) {
            return $squad->getSquadIndex() === $this->getIndexNextSquad();
        });
        if (count($currentSquads) > 0) {
            return array_pop($currentSquads);
        }

        return null;
    }

    public function selectNextPlayer(): ?GameParticipant
    {
        dump($this->indexNextPlayer);
        dump($this->participants);
        die;

    }

    public function getCurrentPlayer(): ?GameParticipant
    {
        if ($this->indexNextPlayer === null) return null;
        $players = $this->getParticipants()->toArray();
        $nextPlayers = array_filter($players, function (GameParticipant $participant) {
            return $participant->getSpotIndex() === $this->indexNextPlayer;
        });
        if (count($nextPlayers) === 0) return null;

        return array_pop($nextPlayers);
    }

    public function isCurrentPlayer(User $user): bool
    {
        if ($this->getCurrentPlayer() === null) return false;

        return $this->getCurrentPlayer()->getAppUser() === $user;
    }

    public function getIndexNextSquad(): ?int
    {
        return $this->indexNextSquad;
    }

    public function setIndexNextSquad(?int $indexNextSquad): self
    {
        $this->indexNextSquad = $indexNextSquad;

        return $this;
    }

    public function getIsStarted(): ?bool
    {
        return $this->isStarted;
    }

    public function setIsStarted(?bool $isStarted): self
    {
        $this->isStarted = $isStarted;

        return $this;
    }

}
