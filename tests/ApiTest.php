<?php
/**
 * Created by PhpStorm.
 * Author: Damian Barczyk
 * Date: 29/01/2019
 * Time: 20:22
 */

namespace App\Test;

use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\TeamFixtures;

class ApiTest extends WebTestCase
{
    private $em;
    private $client;

    /**
     * Prepare and clean database
     */
    protected function setUp()
    {
        parent::setUp(); //
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $this->em=$entityManager;


        $loader = new Loader();
        $loader->addFixture(new TeamFixtures());
        $loader->addFixture(new UserFixtures());

        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->purge();
        $entityManager->getConnection()->exec("delete from sqlite_sequence where name='team';");
        $entityManager->getConnection()->exec("delete from sqlite_sequence where name='league';");
        $executor->execute($loader->getFixtures(), true);


    }
    /*
    1.	Get a list of football teams in given league
    2.	Create a football team
    3.	Replace all attributes of a football team
    4.	Delete a football league
    */

    /**
     * Test get team from non existen league
     */
    public function testGetTeamsBadLeague()
    {
        $this->client->request('GET', '/api/teams/NonExitestLeague');
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Get team in leage
     */
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

    /**
     *  Create team
     */
    public function testCreateTeam()
        {
            $postdata=array('name'=>"Test Team",'strip'=>"Some Strip");
            $this->client->request('POST', '/api/createteam/2',$postdata);
            $response = $this->client->getResponse();
            $this->assertTrue(
                $response->isSuccessful(),
                'response status is 2xx'
            );

            $this->assertTrue(
                $response->headers->contains(
                    'Content-Type',
                    'application/json'
                ),
                'the "Content-Type" header is "application/json"' // optional message shown on failure
            );
        }

    /**
     * Create team without name
     */
    public function testUpdateTeamBadData()
    {

        $postdata = array('leagueId' => 11);
        $this->client->request('PUT', '/api/team/5', $postdata);
        $response = $this->client->getResponse();
        $this->assertEquals(421, $response->getStatusCode());


    }

    /**
     * Update team
     */
    public function testUpdateTeam()
    {
        $postdata=array('name'=>"Test Team",'strip'=>"Some Strip",'leagueId'=>11);
        $this->client->request('PUT', '/api/team/5',$postdata);
        $response = $this->client->getResponse();
        $this->assertTrue(
            $response->isSuccessful(),
            'response status is 2xx'
        );

        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );
    }

    /**
     * Delete team
     */
    public function testDeleteTeam()
    {
        $this->client->request('DELETE', '/api/team/5');
        $response = $this->client->getResponse();
        $this->assertTrue(
            $response->isSuccessful(),
            'response status is 2xx'
        );
    }

}