<?php

namespace App\Controller;

use App\Entity\League;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LeagueRepository;
use App\Service\TeamService;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/teams/{leagueName}", methods={"GET"}, name="api_team_list")
     */
    public function teamList(LeagueRepository $leagueRepository, $leagueName)
    {
       $league=$leagueRepository->findOneBy(array('name' => preg_replace("/[^[:alnum:][:space:]]/u", "",$leagueName)));

        if (!$league) {
            return $this->json(['error'=>'League not exists'], $status = 404);
        }

        $teams=$league->getTeams();
        foreach ($teams as $team) {
            $results[] = self::teamPrepare($team);
        }

        return $this->json($results);

    }

    /**
     * Create team in League (get league id )
     * @Route("/api/createteam/{id}", methods={"POST"}, name="api_team_create", requirements={"id"="\d+"})
     */
    public function teamCreate(Request $request, TeamService $teamService,  League $league)
    {

        try {
            $data = [
                'name' => $request->request->get('name'),
                'strip' => $request->request->get('strip')
            ];
            $team=$teamService->createTeam($data,$league);

            return $this->json(array(
                'status' => 'Success',
                'team' => self::teamPrepare($team)));

        } catch (\Exception $e) {
            return $this->json(
                array('status' => $e->getMessage()),
                421
            );
        }
    }

    /**
     * @Route("/api/team/{id}", methods={"PUT"}, name="api_team_update")
     */
    public function teamUpdate(Request $request, TeamService $teamService, Team $team)
    {
        try {
            $data =[
                'name' => $request->request->get('name'),
                'strip' =>$request->request->get('strip'),
                'leagueId' => $request->request->get('leagueId'),
                ];

            $updatedTeam=$teamService->updateTeam($team,$data);

            return $this->json(
                array(
                    'status' => 'Success',
                    'team'   => self::teamPrepare($updatedTeam)
                )
            );
        } catch (\Exception $e) {
            return $this->json(
                array('error' => $e->getMessage()),
                421
            );
        }
    }

    /**
     * @Route("/api/team/{id}", methods={"DELETE"}, name="api_team_delete")
     */
    public function teamDelete(Team $team)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($team);
        $entityManager->flush();

        return $this->json(
            array('status' => 'Success'));
    }


    private static function  teamPrepare(Team $team) {
        return [
            'Id' => $team->getId(),
            'name' => $team->getName(),
            'strip' =>$team->getStrip(),
            'league' => $team->getLeague()->getName(),
            'leagueId' => $team->getLeague()->getId(),
        ];
    }

}
