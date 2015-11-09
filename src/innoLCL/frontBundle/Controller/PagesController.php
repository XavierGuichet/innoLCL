<?php

namespace innoLCL\frontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PagesController extends Controller
{
    public function mentionsAction(Request $request) // /mentions-legales
    {
        return $this->render('innoLCLfrontBundle:Pages:mentions.html.twig');
    }
    
    public function reglementAction(Request $request) // /reglement
    {
        return $this->render('innoLCLfrontBundle:Pages:reglement.html.twig');
    }
    
    public function manualAction(Request $request) // /mode d'emploi
    {
        return $this->render('innoLCLfrontBundle:Pages:manual.html.twig');
    }
}
