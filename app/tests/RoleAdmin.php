<?php

namespace App\Tests;

trait RoleAdmin
{

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'aa@aa.pl',
            'PHP_AUTH_PW' => 'pass'
        ]);
        $this->client->disableReboot();

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }
}
