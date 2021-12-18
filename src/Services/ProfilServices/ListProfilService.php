<?php

namespace App\Services\ProfilServices;

use App\Entity\Admin;
use App\Entity\Profil;
use App\Entity\ProfilAdmin;
use App\Entity\User;
use App\Repository\AdminRepository;
use App\Repository\ProfilAdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ProxyManager\Factory\RemoteObject\Adapter\JsonRpc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ListProfilService
{

    private $em;
    private $profilRepository;
    private $validator;
    private $hasher;
    private $serializer;

    public function __construct(
        EntityManagerInterface $em,
        ProfilAdminRepository $profilRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
        $this->hasher = $hasher;
        $this->serializer = $serializer;
    }
    public function listProfil(?Admin $admin, ?User $user): JsonResponse
    {
        $list = $this->profilRepository->findAll();
        $json = $this->serializer->serialize($list, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], ['groups' => 'Admin:read']));
        return new JsonResponse($json, Response::HTTP_OK, [], true);
        
    }

    
}