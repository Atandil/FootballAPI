<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LeagueRepository;
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
        $results = [];
        foreach ($teams as $team) {
            $results[] = [
                'ID' => $team->getId(),
                'name' => $team->getName(),
                'strip' =>$team->getStrip(),
            ];
        }

        return $this->json($results);

    }
}
