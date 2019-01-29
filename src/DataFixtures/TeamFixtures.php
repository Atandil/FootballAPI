<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Team;
use App\Entity\League;

class TeamFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        foreach ($this->getData() as $leagueName=>$teams) {

            $league = new League();
            $league->setName($leagueName);


            foreach ($teams as [$teamName, $strip]) {
                $team = new Team();
                $team->setName($teamName);
                $team->setStrip($strip ? $strip : null);
                $league->addTeam($team);
            }

            $manager->persist($league);
        }

        $manager->flush();
    }

    private function getData(): array
    {

        $arr['Premier League']=[
                         ['Arsenal','Blue and Red'],
                         ['Liverpool','Red'],
                         ['Newcastle','Black'],
                         ['Manchester City','White strip'],
                         ['Manchester United','Yellow'],
                         ['Fulham','Blue'],
                         ['Cardif','Pink'],
                        ];

        $arr['English Football League Championship']=[
            ['Bradford','Wasp'],
            ['Bristol',''],
            ['Hull','Tiger'],
        ];

        $arr['National League']=[
            ['Barrow','Blue and Red'],
            ['Bow','Red'],
            ['Dover','Black'],

        ];

        return $arr;
    }

}
