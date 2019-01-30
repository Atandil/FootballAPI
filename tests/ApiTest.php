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

    protected function setUp()
    {
        parent::setUp(); //
        $client = static::createClient();
        $container = $client->getContainer();
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

    public function testIndex()
    {
        $this->assertTrue(true);
    }






}