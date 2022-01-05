<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FrancisController extends AbstractController
{
    /**
     * @Route("/")
     * @return Response
     */
    public function homepage(Environment     $twig,
                             LoggerInterface $debugLogger,
                             MarkdownHelper  $helper,
                             bool            $isDebug)
    {
        $reponses = [
            'je suis une **première** réponse',
            'et moi ``une seconde``',
            'peut-etre même une 3eme'
        ];

        dump($isDebug);

        $title = "Ma **pizza** est ``froide``";

        $retour = $twig->render("Frontend/home.html.twig", ['reponses' => $reponses, 'titre' => $title]);

        $debugLogger->info('je suis un message de log');

        return new Response($retour);
    }

    /**
     * @Route("/question/{slug}", name="app_showone")
     * @return Response
     */
    public function showOne(Question $question)
    {
        if (!$question) {
            throw $this->createNotFoundException('un message d\'erreur');
        }

        return $this->render('Frontend/show.html.twig', [
            'question' => $question
        ]);
    }

    /**
     * @Route("/endpoint")
     * @return JsonResponse
     */
    public function endpoint()
    {
        $reponses = [
            'je suis une première réponse',
            'et moi une seconde',
            'peut-etre même une 3eme'
        ];

        return $this->json($reponses);
    }

    /**
     * @Route("question/new", name="app_newQuestion")
     * @return Response
     */
    public function newQuestion(EntityManagerInterface $entityManager): Response
    {
        $question = (new Question())
            ->setName('Comment rendre une pizza ?')
            ->setSlug('comment-rendre-une-pizza' . rand(0, 1000))
            ->setQuestion('Ma pizza est froide et j\'aimerais bien la rendre, quel est le numero du SAV ?')
            ->setAskedAt(new \DateTime(sprintf('-%d days', rand(1, 100))));

        $entityManager->persist($question);
        $entityManager->flush();

        return new Response(sprintf('nouvelle question avec le slug : %s', $question->getSlug()));
    }
}