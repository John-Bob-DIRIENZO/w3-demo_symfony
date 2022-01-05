<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FrancisController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     * @return Response
     */
    public function homepage(Environment        $twig,
                             LoggerInterface    $debugLogger,
                             MarkdownHelper     $helper,
                             bool               $isDebug,
                             QuestionRepository $questionRepository)
    {
        $questions = $questionRepository->findAllPublishedOrderByNewest();

        return $this->render('Frontend/home.html.twig', ["questions" => $questions]);
    }


    /**
     * @Route("/question/search", name="app_question_search")
     * @return Response
     */
    public function searchQuestion(Request $request, QuestionRepository $questionRepository)
    {
        $search = $request->query->get('name');

        return $this->render('Frontend/home.html.twig', ["questions" => $questionRepository->searchByName($search)]);
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

    /**
     * @Route("/question/{slug}/vote", name="app_question_vote", methods={"POST"})
     * @param Question $question
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $vote = $request->request->get('vote');

        if ($vote === "up") {
            $question->upVote();
        } else {
            $question->downVote();
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_showone', [
            'slug' => $question->getSlug()
        ]);
    }

    /**
     * @Route("/question/{id}/delete", name="app_question_delete")
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     * @throws ORMException
     */
    public function questionDelete($id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $question = $entityManager->getReference(Question::class, $id);
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/question/{slug}", name="app_showone")
     * @param Question $question
     * @return Response
     */
    public function showOne(Question $question): Response
    {
        if (!$question) {
            throw $this->createNotFoundException('un message d\'erreur');
        }

        return $this->render('Frontend/show.html.twig', [
            'question' => $question
        ]);
    }
}