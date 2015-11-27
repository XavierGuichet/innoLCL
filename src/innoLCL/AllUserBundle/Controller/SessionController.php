<?php
// src/UserBundle/Controller/SessionController.php

namespace  innoLCL\AllUserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;


class SessionController extends BaseController
{  
	
    public function checkAction()
    {
		$request = $this->container->get('request');
		if($request->isXmlHttpRequest()) {
			$session = $this->container->get('session');
						
			$start = $session->getMetadataBag()->getCreated();		
			$lastused = $session->getMetadataBag()->getLastUsed();			
			$livedtime = $lastused - $start;
						
			$response = new JsonResponse(array('success' => array('timesession' => $livedtime)), 200);
		}
        return $response;
    }
}
