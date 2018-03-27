<?php

namespace Kasperski\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('UserBundle:User:index.html.twig');
    }
}
