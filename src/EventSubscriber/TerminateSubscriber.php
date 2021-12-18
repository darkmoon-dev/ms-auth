<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Services\Messagerie\MailerController;
use App\Entity\Abonne;
use App\Entity\Demande;
use App\Entity\Devis;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;
use App\Repository\DemandeRepository;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use App\Repository\FactureRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class TerminateSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $serializer;
    private  $twig;
    private $demandeRepository;
    private $userRepository;
    private $devisRepository;
    private $factureRepository;
    private $transactionRepository;
    public function __construct(
        MailerController $mailer,
        SerializerInterface $serializer,
        Environment $twig
        
    ) {
        $this->mailer = $mailer;
        $this->serializer = $serializer;
        $this->twig = $twig;
    
    }
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::TERMINATE => [
                ['terminateProcess', 10]
            ] ,
           /* KernelEvents::RESPONSE => [
                ['customResponse',0],
            ],*/
        ];
    }
    public function customResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($response->getContent());
       // dd($response);
        //$response->header->set("message","fd");
    
        $response->setStatusCode($response->getStatusCode());
        $event->setResponse($response);
    }

    public function terminateProcess(TerminateEvent $event)
    {
        $request = $event->getRequest();
        $content = $request->getContent();
        $response = $event->getResponse();
        $resContent = $response->getContent();
        $url = $request->getUri();
        $uri = $request->getRequestUri();
        $method = $request->getMethod();
        $error = "";
        $abonne = new User();
       

        $recipient = "antoine03kaboome@gmail.com";


        //mail à envoyer pour la validation du compe abonné
        if ($uri == "/api/create/user" && $method == "POST" && $response->getStatusCode() == Response::HTTP_CREATED) {
            try {
                $user = $this->serializer->deserialize($resContent, User::class, 'json');
                $recipient = $user->getEmail();
                //  $abonne->getCodeR
            } catch (Exception $ex) {
                $error = $ex->getMessage();
            }
            //$random = random_int(1, 10);
            $random = rand(10000,99999);
            $content = $this->twig->render('account_validation.html.twig', [
                'user' => $user,
                "logo" => $request->getSchemeAndHttpHost() . "/images/logo_bline.png",
                //'code_recu' => $random
            ]);
            $this->mailer->sendEmail("Validation de compte", $content, $recipient);
        }

        
       
    }
}