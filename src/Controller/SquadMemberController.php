<?php

namespace App\Controller;

use App\Entity\GameParticipant;
use App\Entity\Squad;
use App\Entity\SquadMember;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use function array_filter;
use function count;

/**
 * Class SquadMemberController
 * @package App\Controller
 * @Route("/squad")
 */
class SquadMemberController extends AbstractController
{
    private function endSquadIfCompleted(Squad $squad)
    {
        $isSquadPlayed = true;
        foreach ($squad->getSquadMembers() as $member) {
            if ($member->getSpyPlayed() === null) {
                $isSquadPlayed = false;
            }
        }
        if ($isSquadPlayed) {
            $squad->setStatus(Squad::STATUS_PLAYED);
            $isGameOver = $squad->getGame()->getIndexNextSquad() + 1 === $squad->getGame()->getSquads()->count() || $squad->getGame()->isGameFinished();

            if ($isGameOver) {
                $squad->getGame()->setIsOver(true);
                $squad->getGame()->setIndexNextSquad(-1);
            } else {
                $nextSquads = array_filter($squad->getGame()->getSquads()->toArray(), function (Squad $squad) {
                    return $squad->getSquadIndex() === $squad->getGame()->getIndexNextSquad() + 1;
                });
                if (count($nextSquads) > 0) {
                    $squad->getGame()->setIndexNextSquad($squad->getGame()->getIndexNextSquad() + 1);
                }

                $nextPlayers = array_filter($squad->getGame()->getParticipants()->toArray(), function (GameParticipant $participant) {
                    return $participant->getSpotIndex() === $participant->getGame()->getIndexNextPlayer() + 1;
                });
                if (count($nextPlayers) > 0) {
                    $squad->getGame()->setIndexNextPlayer($squad->getGame()->getIndexNextPlayer() + 1);
                } else {
                    $squad->getGame()->setIndexNextPlayer(0);
                }
            }
        }
    }

    /**
     * @Route("/{id}/play-spy", name="play_spy", methods={"GET"})
     * @param SquadMember            $squadMember
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function playSpy(SquadMember $squadMember, EntityManagerInterface $em)
    {
        $squadMember->setSpyPlayed(true);
        $this->endSquadIfCompleted($squadMember->getSquad());
        $em->flush();

        return new RedirectResponse($this->generateUrl('squad', ['id' => $squadMember->getSquad()->getId()]));
    }

    /**
     * @Route("/{id}/play-resistant", name="play_resistant", methods={"GET"})
     * @param SquadMember            $squadMember
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function playResistant(SquadMember $squadMember, EntityManagerInterface $em)
    {
        $squadMember->setSpyPlayed(false);
        $this->endSquadIfCompleted($squadMember->getSquad());
        $em->flush();

        return new RedirectResponse($this->generateUrl('squad', ['id' => $squadMember->getSquad()->getId()]));
    }
}
