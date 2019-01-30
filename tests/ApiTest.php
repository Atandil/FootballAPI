<?php
/**
 * Created by PhpStorm.
 * Author: Damian Barczyk
 * Date: 29/01/2019
 * Time: 20:22
 */

namespace App\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\TeamFixtures;

class ApiTest extends WebTestCase
{
    private $em;
    private $client;

    protected function setUp()
    {
        parent::setUp(); //
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();
        $this->em=$entityManager;


        $fixture = new TeamFixtures();
        $fixture->load($entityManager);

    }
    /*
    1.	Get a list of football teams in given league
    2.	Create a football team
    3.	Replace all attributes of a football team
    4.	Delete a football league
    */

    public function testGetTeamsBadLeague()
    {
        $this->client->request('GET', '/api/teams/NonExitestLeague');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetTeamsExistingLeague()
    {
        $this->client->request('GET', '/api/teams/Premier League');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'response status is 2xx');

        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );






    }


    public function testSetUp()
    {
        $this->assertTrue(true);
    }






}