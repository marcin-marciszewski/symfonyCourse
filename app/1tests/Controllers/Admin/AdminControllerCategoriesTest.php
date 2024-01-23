<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Category;


use App\Tests\RoleAdmin;


class AdminControllerCategoriesTest extends WebTestCase
{

    use RoleAdmin;

    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $this->assertCount(21, $crawler->filter('option'));
    }

    public function testAddNewCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $form = $crawler->selectButton('Add')->form([
            'category[parent]' => 1,
            'category[name]' => 'New'
        ]);

        $this->client->submit($form);

        $category = $this->em->getRepository(Category::class)->findOneBy(['name' => 'New']);

        $this->assertNotNull($category);
        $this->assertSame('New', $category->getName());
    }

    public function testEditCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/edit-category/1');

        $form = $crawler->selectButton('Save')->form(
            [
                'category[parent]' => 0,
                'category[name]' => 'Electronics 2'
            ]
        );

        $this->client->submit($form);

        $category = $this->em->getRepository(Category::class)->find(1);

        $this->assertSame('Electronics 2', $category->getName());
    }

    public function testDeleteCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/delete-category/1');

        $category = $this->em->getRepository(Category::class)->find(1);

        $this->assertNull($category);
    }
}
