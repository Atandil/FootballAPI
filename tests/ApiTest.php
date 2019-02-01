<?php
/**
 * Created by PhpStorm.
 * Author: Damian Barczyk
 * Date: 29/01/2019
 * Time: 20:22
 */

namespace App\Test;

use App\Entity\User;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\TeamFixtures;

class ApiTest extends WebTestCase
{
    private $em;
    private $client;
    private $token;

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

        //Preprare fresh database
        $teamFixture=new TeamFixtures();
        $entityManager->getConnection()->exec("delete from team;");
        $entityManager->getConnection()->exec("delete from league;");
        $entityManager->getConnection()->exec("delete from sqlite_sequence where name='team';");
        $entityManager->getConnection()->exec("delete from sqlite_sequence where name='league';");
        $teamFixture->load($entityManager);

        //prepare Auth Token
        $user=$this->em
            ->getRepository(User::class)
            ->find(1);

        $this->token=$this->jwt($user);

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
    public function testNoAuthorized()
    {
        $this->client->request('GET', '/api/teams/test');
        $response = $this->client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
    }


    public function testGetNewToken()
    {
        $this->client->request('POST', '/token/get', ['username'=>'admin@gov.uk','password'=>'password']);
        $response = $this->client->getResponse();
        $this->assertTrue(
            $response->isSuccessful(),
            'response status is 2xx'
        );
        $finishedData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $finishedData);
    }

    public function testGetNewTokenBadUsername()
    {
        $this->client->request('POST', '/token/get',['username'=>'bad@user','password'=>'hacker']);
        $response = $this->client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
    }


    /**
     * Test get team from non existen league
     */
    public function testGetTeamsBadLeague()
    {
        $this->client->request('GET', '/api/teams/NonExitestLeague',[],[],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token
            ]);
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Get team in leage
     */
    public function testGetTeamsExistingLeague()
    {
        $this->client->request('GET', '/api/teams/Premier League',[],[],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token
            ]);
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
            $this->client->request('POST', '/api/createteam/2',$postdata,[],
                [
                    'HTTP_AUTHORIZATION' => 'Bearer '.$this->token
                ]);
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
        $this->client->request('PUT', '/api/team/5', $postdata,[],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token
            ]);
        $response = $this->client->getResponse();
        $this->assertEquals(421, $response->getStatusCode());


    }

    /**
     * Update team
     */
    public function testUpdateTeam()
    {
        $postdata=array('name'=>"Test Team",'strip'=>"Some Strip",'leagueId'=>11);
        $this->client->request('PUT', '/api/team/5',$postdata,[],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token
            ]);
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
        $this->client->request('DELETE', '/api/team/5',[],[],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '.$this->token
            ]);
        $response = $this->client->getResponse();
        $this->assertTrue(
            $response->isSuccessful(),
            'response status is 2xx'
        );
    }

    private function jwt(User $user) {
        $payload = [
            'iss' => "symfony-jwt", // Issuer of the token
            'sub' => $user->getUsername(), // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60*60 // Expiration time
        ];

        $secret=getenv('JWT_SECRET');
        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, $secret);
    }


}