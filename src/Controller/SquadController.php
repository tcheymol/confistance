<?php

namespace App\Controller;

use App\Entity\GameParticipant;
use App\Entity\Squad;
use App\Entity\SquadMember;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use function array_filter;
use function array_map;
use function array_rand;
use function count;

/**
 * Class SquadController
 * @package App\Controller
 * @Route("/squad")
 */
class SquadController extends AbstractController
{
    /**
     * @Route("/{id}/select-player/{participant_id}", name="squad_select_member", methods={"GET"})
     * @ParamConverter("squad", options={"id" = "id"})
     * @ParamConverter("gameParticipant", options={"id" = "participant_id"})
     * @param Squad                  $squad
     * @param GameParticipant        $gameParticipant
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function selectPlayerForNextSquad(Squad $squad, GameParticipant $gameParticipant, EntityManagerInterface $em)
    {
        $game = $squad->getGame();
        $squadMembers = $squad->getSquadMembers();

        foreach ($squadMembers as $squadMember) {
            if ($squadMember->getMember() === $gameParticipant->getAppUser()) {
                $em->remove($squadMember);
                $em->flush();

                return new RedirectResponse($this->generateUrl('game', ['id' => $game->getId()]));
            }
        }

        if (count($squad->getSquadMembers()) >= $squad->getSpotsCount()) {
            $this->addFlash('current-squad', 'Escouade pleine');

            return new RedirectResponse($this->generateUrl('game', ['id' => $game->getId()]));
        }

        $squadMember = new SquadMember();
        $squadMember->setSquad($squad);
        $squadMember->setMember($gameParticipant->getAppUser());
        $em->persist($squadMember);
        $squad->addSquadMember($squadMember);
        $em->flush();

        return new RedirectResponse($this->generateUrl('game', ['id' => $game->getId()]));
    }

    /**
     * @Route("/{id}/start", name="start_squad", methods={"GET"})
     * @param Squad                  $squad
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function start(Squad $squad, EntityManagerInterface $em)
    {
        $squad->setStatus(Squad::STATUS_PLAYING);
        $em->flush();

        return $this->redirect($this->generateUrl('squad', ['id' => $squad->getId()]));

    }

    /**
     * @Route("/{id}/refuse", name="refuse_squad", methods={"GET"})
     * @param Squad                  $squad
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function refuse(Squad $squad, EntityManagerInterface $em)
    {
        $nextPlayers = array_filter($squad->getGame()->getParticipants()->toArray(), function (GameParticipant $participant) {
            return $participant->getSpotIndex() === $participant->getGame()->getIndexNextPlayer() + 1;
        });
        if (count($nextPlayers) > 0) {
            $squad->getGame()->setIndexNextPlayer($squad->getGame()->getIndexNextPlayer() + 1);
        } else {
            $squad->getGame()->setIndexNextPlayer(0);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('game', ['id' => $squad->getGame()->getId()]));

    }

    /**
     * @Route("/{id}", name="squad", methods={"GET"})
     * @param Squad    $squad
     * @param Security $security
     * @return Response
     */
    public function index(Squad $squad, Security $security)
    {
        if ($squad->getSquadIndex() !== $squad->getGame()->getIndexNextSquad()) {
            return $this->redirect($this->generateUrl('squad_result', ['id' => $squad->getId()]));
        }

        return $this->render('squad/index.html.twig', [
            'squad' => $squad,
            'userSquadMember' => $squad->getUserSquadMember($security->getUser()),
        ]);
    }

    /**
     * @Route("/{id}/result", name="squad_result", methods={"GET"})
     * @param Squad $squad
     * @return Response
     */
    public function result(Squad $squad)
    {
        $squadMembers = $squad->getSquadMembers();
        $squadResults = array_map(function (SquadMember $member) {
            return $member->getSpyPlayed();
        }, $squadMembers->toArray());

        $unorderedSquadResults = [];
        $squadResultsCount = count($squadResults);
        for ($i = 0; $i < $squadResultsCount; $i++) {
            $randomIndex = array_rand($squadResults);
            $unorderedSquadResults[] = $squadResults[$randomIndex];
            unset($squadResults[$randomIndex]);
        }

        return $this->render('squad/result.html.twig', [
            'squad' => $squad,
            'results' => $unorderedSquadResults,
        ]);
    }
}
