<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
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

        $title = $helper->parse("Ma **pizza** est ``froide``");

        $retour = $twig->render("Frontend/home.html.twig", ['reponses' => $reponses, 'titre' => $title]);

        $debugLogger->info('je suis un message de log');

        return new Response($retour);
    }

    /**
     * @Route("/show/{ma_wildcard}-{autre_truc}", name="app_showone")
     * @return Response
     */
    public function showOne($ma_wildcard, $autre_truc)
    {
        return new Response(sprintf('le param passé : %s et %s', $ma_wildcard, $autre_truc));
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
}