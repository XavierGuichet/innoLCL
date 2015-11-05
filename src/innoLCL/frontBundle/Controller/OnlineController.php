<?php

namespace innoLCL\frontBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlineController extends Controller
{
    public function indexAction($type, $hash) {
        
        $dir="";
        if($type == "mail") $dir.= $this->get('kernel')->getRootDir()."/../web/".$this->container->getParameter('online_mail_dir');
        
        $file="none.dat";
        if($hash){ $file = $hash.'.dat'; }
        
        if(!file_exists($dir.$file)){
            throw $this->createNotFoundException('Accès impossible.');
        }
        
        $view = file_get_contents($dir.$file);
        
        return new Response($view);
    }
}
