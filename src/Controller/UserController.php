<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Flight;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
/**
 * @Route("/", name="user_")
 */
{   /**
    *@Route("/profile/{{id}}", name="profile")
    */
    public function userProfile(UserRepository $user,int $id): Response
    {
        return $this->render('user/user-profile.html.twig', [
            'user' => $user->find($id),
            'website' => 'Flight Advisor'
        ]);
    }

    /** 
     * @Route("/profile/edit/{{id}}", name="edit")
     */
    public function editProfile(): Response
    {
        return $this->render('user/edit.html.twig',);
    }

}