<?php

namespace App\Tests;

use App\Tests\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerCommentsTest extends WebTestCase
{
    use RoleAdmin;

    public function testNotLoggedInUser(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/video-details/16');

        $form = $crawler->selectButton('Add')->form(['comment' => 'Test Comment']);
        $client->submit($form);

        $this->assertStringContainsString('Please sign in', $client->getResponse()->getContent());
    }

    public function testNewCommentAndNumberOfComments(): void
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/video-details/16');

        $form = $crawler->selectButton('Add')->form(['comment' => 'Test Comment']);
        $this->client->submit($form);

        $this->assertStringContainsString('Test Comment',  $this->client->getResponse()->getContent());

        $crawler = $this->client->request('GET', '/video-list/category/toys,2');
        $this->assertStringContainsString('Comments (1)',  $crawler->filter('a.ml-1')->text());
    }
}
