<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerSecurityTest extends WebTestCase
{
    /**
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertStringContainsString('/login', $client->getResponse()->getTargetUrl());
    }
    /**
     * @return \Generator|string[]
     */
    public function getSecureUrls()
    {
        yield ['/admin/videos'];
        yield ['/admin'];
        yield ['/admin/su/categories'];
        yield ['/admin/su/delete-category/1'];
    }

    public function testVideoForMembersOnly()
    {
        $client = static::createClient();
        $client->request('GET', '/video-list/category/movies,4');
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $client->getResponse()->getContent());
    }
}
