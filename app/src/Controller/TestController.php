<?php

namespace App\Controller;

use App\Entity\TestEntity;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test',  methods: ['POST'])]
    public function index(Request $request, SerializerInterface $serializer): Response
    {
        // $serializer = SerializerBuilder::create()->build();
        // "Could not instantiate attribute "Symfony\Component\Serializer\Annotation\Context" on class App\Entity\TestEntity."
        $content = $request->getContent();
        // $one = $serializer->deserialize($content, TestEntity::class, 'json');
        $object = $serializer->deserialize($content, TestEntity::class, 'json');
        // return $this->json('o');
        return new Response(null, 200);
    }
}
