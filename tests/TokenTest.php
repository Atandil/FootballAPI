<?php
/**
 * Created by PhpStorm.
 * Author: Damian Barczyk
 * Date: 30/01/2019
 * Time: 11:56
 */

namespace App\Test;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;

class TokenTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        parent::setUp(); //
        $this->client = static::createClient();
    }
    public function testIndexJsonResponse()
    {
        $this->client->request('GET', '/');

        $response = $this->client->getResponse();

        $this->assertJson('{"message":"Football API"}',$response);
    }

    public function testGetToken()
    {

    }


}
