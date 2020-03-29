<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameParticipant;
use App\Entity\Squad;
use App\Form\GameType;
use App\Repository\GameParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use function array_rand;

/**
 * Class GameController
 * @package App\Controller
 * @Route("/game")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/new", name="create_game", methods={"POST", "GET"})
     * @param Request                $request
     * @param Security               $security
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function create(Request $request, Security $security, EntityManagerInterface $em)
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $game->setCreator($security->getUser());
            $game->setIsOver(false);
            $em->persist($game);

            $sits = range(0, $game->getNumberOfParticipants() - 1);

            for ($i = 0; $i < $game->getNumberOfParticipants(); $i++) {
                $participant = new GameParticipant();
                $game->addParticipant($participant);
                $participant->setGame($game);
                $participant->setSpotIndex($i);
                $participant->setIsSpy(false);
                $em->persist($participant);
            }

            $participants = $game->getParticipants();

            for ($i = 1; $i <= $game->getSpiesCount(); $i++) {
                $randomSitIndex = array_rand($sits);
                $spy = $participants[$randomSitIndex];
                $spy->setIsSpy(true);
                unset($sits[$randomSitIndex]);
            }

            $squads = $game->getSquadsDistribution();
            foreach ($squads as $index => $squadMembersCount) {
                $squad = new Squad();
                $squad->setGame($game)->setSpotsCount($squadMembersCount)->setSquadIndex($index);
                $em->persist($squad);
            }

            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('game/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="game", methods={"GET"})
     * @param Game                      $game
     * @param Security                  $security
     * @param GameParticipantRepository $repository
     * @return Response
     */
    public function index(Game $game, Security $security, GameParticipantRepository $repository)
    {
        $user = $security->getUser();
        $currentUserParticipant = $repository->findOneBy(['game' => $game, 'appUser' => $user]);

        return $this->render('game/index.html.twig', [
            'game' => $game,
            'currentUserParticipant' => $currentUserParticipant
        ]);
    }

    /**
     * @Route("/join/{id}", name="join_game", methods={"GET"})
     * @param Game                   $game
     * @param Security               $security
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function join(Game $game, Security $security, EntityManagerInterface $em)
    {
        $user = $security->getUser();
        $spots = $game->getParticipants();

        foreach ($spots as $spot) {
            if ($spot->getAppUser() !== null && $spot->getAppUser()->getId() === $user->getId()) {
                return new RedirectResponse($this->generateUrl('game', ['id' => $game->getId()]));
            }
        }
        foreach ($spots as $spot) {
            if ($spot->getAppUser() === null) {
                $spot->setAppUser($user);
                $em->flush();
                break;
            }
        }

        return new RedirectResponse($this->generateUrl('game', ['id' => $game->getId()]));
    }

    /**
     * @Route("/start/{id}", name="start_game", methods={"GET"})
     * @param Game                   $game
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function start(Game $game, EntityManagerInterface $em)
    {
        $firstPlayer = array_rand($game->getParticipants()->toArray());
        $game->setIndexNextPlayer($game->getParticipants()[$firstPlayer]->getSpotIndex());
        $game->setIsStarted(true);
        $em->flush();

        return new RedirectResponse($this->generateUrl('game', ['id' => $game->getId()]));
    }

    /**
     * @Route("/{id}/leave", name="leave_game", methods={"GET"})
     * @param Game                   $game
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function leave(Game $game, EntityManagerInterface $em, Security $security)
    {
        $user = $security->getUser();
        $userGameParticipants = $game->getParticipants()->filter(function (GameParticipant $participant) use ($user) {
            return $participant->getAppUser() === $user;
        });

        $userParticipant = $userGameParticipants->first();
        if ($userParticipant !== false) {
            $userParticipant->setAppUser(null);
        }

        $em->flush();

        return new RedirectResponse($this->generateUrl('dashboard'));
    }

    /**
     * @Route("/next-squad/{id}", name="game_next_squad", methods={"GET"})
     * @param Game $game
     * @return RedirectResponse
     */
    public function nextSquad(Game $game)
    {
        return new RedirectResponse($this->generateUrl('game', ['id' => $game->getId()]));
    }
}
