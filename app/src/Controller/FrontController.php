<?php
// bin/console doctrine:schema:drop -n -q --force --full-database && rm ./migrations/*.php && bin/console make:migration && bin/console doctrine:migrations:migrate -n -q && bin/console doctrine:fixtures:load -n -q
namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Entity\Category;
use App\Entity\Comment;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FrontController extends AbstractController
{
    public function __construct(private TokenStorageInterface $tokenStorage, private RequestStack $requestStack, private ManagerRegistry $doctrine)
    {
    }
    #[Route('/', name: 'main_page')]
    public function index(): Response
    {

        return $this->render('front/index.html.twig');
    }

    #[Route('/video-list/category/{categoryname},{id}/{page}', name: 'video_list', defaults: ['page' => "1"])]
    public function videoList($id, $categoryname, $page, CategoryTreeFrontPage $categories, Request $request): Response
    {
        $categories->getCategoryListAndParent($id);
        $ids = $categories->getChildIds($id);
        array_push($ids, $id);
        $videos = $this->doctrine->getRepository(Video::class)->findByChildIds($ids, $page, $request->get('sortby'));
        return $this->render('front/video_list.html.twig', ['subcategories' => $categories, 'categoryname' => $categoryname, 'videos' => $videos]);
    }



    #[Route('/video-details/{video}', name: 'video_details')]
    public function videoDetails(VideoRepository $repo, $video): Response
    {
        return $this->render('front/video_details.html.twig', [
            'video' => $repo->videoDetails($video)
        ]);
    }

    #[Route('/search-results/{page}', name: 'search_results', methods: ['GET'], defaults: ['page' => '1'])]
    public function searchResults($page, Request $request): Response
    {
        $videos = null;
        $query = null;
        if ($query = $request->get('query')) {
            $videos = $this->doctrine->getRepository(Video::class)->findByTitle($query, $page, $request->get('sortby'));

            if (!$videos->getItems()) $videos = null;
        }
        return $this->render('front/search_results.html.twig', ['videos' =>  $videos, 'query' =>  $query]);
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request,  UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $user->setName($formData->getName());
            $user->setLastName($formData->getLastName());
            $user->setEmail($formData->getEmail());
            $user->setRoles(['ROLE_USER']);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $formData->getPassword()
            );
            $user->setPassword($hashedPassword);

            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->loginUserAutomatically($user);

            return $this->redirectToRoute('admin_main_page');
        }
        return $this->render('front/register.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $helper): Response
    {
        return $this->render('front/login.html.twig', ['error' => $helper->getLastAuthenticationError()]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new Exception('This should never be reached.');
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/login.html.twig');
    }

    #[Route('/new-comment/{video}', methods: ['POST'], name: 'new_comment')]
    public function newComment(Video $video, Request $request,)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!empty(trim($newComment = $request->request->get('comment')))) {
            $comment = new Comment;
            $comment->setContent($newComment);
            $comment->setUser($this->getUser());
            $comment->setVideo($video);

            $em = $this->doctrine->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('video_details', ['video' => $video->getId()]);
    }
    #[Route('/video-list/{video}/like', methods: ['POST'], name: 'like_video')]
    #[Route('/video-list/{video}/dislike', methods: ['POST'], name: 'dislike_video')]
    #[Route('/video-list/{video}/unlike', methods: ['POST'], name: 'undo_like_video')]
    #[Route('/video-list/{video}/undodislike', methods: ['POST'], name: 'undo_dislike_video')]
    public function toggleLikesAjax(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        switch ($request->get('_route')) {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;

            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;

            case 'undo_like_video':
                $result = $this->undoLikeVideo($video);
                break;

            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
        }

        return $this->json(['action' => $result, 'id' => $video->getId()]);
    }

    public function likeVideo($video)
    {
        $user = $this->doctrine->getRepository(User::class)->find($this->getUser());
        $user->addLikedVideo($video);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return 'liked';
    }

    public function dislikeVideo($video)
    {
        $user = $this->doctrine->getRepository(User::class)->find($this->getUser());
        $user->addDislikedVideo($video);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return 'disliked';
    }

    public function undoLikeVideo($video)
    {
        $user = $this->doctrine->getRepository(User::class)->find($this->getUser());
        $user->removeLikedVideo($video);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return 'undo liked';
    }

    public function undoDislikeVideo($video)
    {
        $user = $this->doctrine->getRepository(User::class)->find($this->getUser());
        $user->removeDislikedVideo($video);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();
        return 'undo disliked';
    }

    public function mainCategories()
    {
        $categories = $this->doctrine->getRepository(Category::class)->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('front/_main_categories.html.twig', ['categories' => $categories]);
    }
    // add explenetion how loginUserAutomatically function works

    public function loginUserAutomatically($user): void
    {
        $token = new UsernamePasswordToken(
            $user,
            'main',
            $user->getRoles()
        );

        $this->tokenStorage->setToken($token);
        $this->requestStack->getSession()->set('_security_main', serialize($token));
    }
}
