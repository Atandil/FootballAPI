<?php
/**
 * Created by PhpStorm.
 * Author: Damian Barczyk
 * Date: 01/02/2019
 * Time: 16:27
 */

namespace App\Service;

use App\Entity\League;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

class TeamService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param         $data array
     *                      $data = [
     *                      'name' => (string) Team name
     *                      'strip' => (string)
     *                      ]
     * @param  League $league
     *
     * @return Team
     * @throws \Exception
     */
    public function createTeam(array $data,League $league)
    {
        if (empty($data['name']) ) {
            throw new \Exception("Name is needed");
        }

        if (!$league) {
            throw  new \Exception("Please show League");
        }

        $team = new  Team();
        $team->setName($data['name']);
        $team->setStrip($data['strip']);
        $team->setLeague($league);

        $this->em->persist($team);
        $this->em->flush();


        return $team;

    }

    /**
     * Update team
     *
     * @param Team $team
     * @param      $data array which contains information about team
     *
     * @return Team
     * @throws \Exception
     */
    public function updateTeam(Team $team, array $data)
    {
        $leagueRepository = $this->em->getRepository('App:League');
        if (isset($data['name'])) {
                $team->setName($data['name']);
        } else {
            throw new \Exception("Minimum name needed");
        }

        if (isset($data['strip'])) {
                $team->setStrip($data['strip']);
        }

        if ($data['leagueId'] && $league = $leagueRepository->find($data['leagueId'])) {
                $team->setLeague($league);
        }

        $this->em->persist($team);
        $this->em->flush();

        return $team;

    }

}