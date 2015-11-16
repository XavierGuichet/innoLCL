<?php
// src/UserBundle/Controller/ResettingController.php

namespace  innoLCL\AllUserBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ResettingController extends BaseController
{  
	
	/**
     * Request reset user password: show form
     */
    public function requestAction()
    {
		$request = $this->container->get('request');
		if($request->isXmlHttpRequest()) {
			$img_path = $this->container->get('templating.helper.assets')->getUrl("images/pwd_retrieve.png", null);
			return new JsonResponse(
				array("title" => "<img src='".$img_path."' alt='Récupération de mot de passe' />",
					  "content" => $this->container->get('templating')->render('innoLCLAllUserBundle:Resetting:request_content.html.twig')));
		}
		else {
			return $this->container->get('templating')->renderResponse('innoLCLAllUserBundle:Resetting:request.html.twig');
		}
    }
    
    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction()
    {
		$request = $this->container->get('request');
        $username = $request->request->get('username');
        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);
        
        if (null === $user) {
			if($request->isXmlHttpRequest()) {
				$img_path = $this->container->get('templating.helper.assets')->getUrl("images/pwd_retrieve.png", null);
				return new JsonResponse(
					array("title" => "<img src='".$img_path."' alt='Récupération de mot de passe' />",
						  "content" => $this->container->get('templating')->render('innoLCLAllUserBundle:Resetting:request_content.html.twig', array('invalid_username' => $username))));
			}
			else {
				return $this->container->get('templating')->renderResponse('innoLCLAllUserBundle:Resetting:request.html.twig');
			}
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
			if($request->isXmlHttpRequest()) {
				$img_path = $this->container->get('templating.helper.assets')->getUrl("images/pwd_already.png", null);
				return new JsonResponse(
					array("title" => "<img src='".$img_path."' alt='Demande déjà effectuée' />",
						  "content" => $this->container->get('templating')->render('innoLCLAllUserBundle:Resetting:passwordAlreadyRequested_content.html.twig')));
			}
			else {
				return $this->container->get('templating')->renderResponse('innoLCLAllUserBundle:Resetting:passwordAlreadyRequested.html.twig');
			}
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

		if($request->isXmlHttpRequest()) {
				$img_path = $this->container->get('templating.helper.assets')->getUrl("images/pwd_success.png", null);
				return new JsonResponse(
					array("title" => "<img src='".$img_path."' alt='Récupération reussie' />",
						  "content" => $this->container->get('templating')->render('innoLCLAllUserBundle:Resetting:checkEmail.html.twig')));
			}
		
		
        return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
    }  
    
    /**
     * Reset user password
     */
    public function resetAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');
        $process = $formHandler->process($user);

        if ($process) {
            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $response = new RedirectResponse($this->container->get('router')->generate('innolcl_front_homepage'));
            $this->authenticateUser($user, $response);

            return $response;
        }

        return $this->container->get('templating')->renderResponse('innoLCLAllUserBundle:Resetting:reset.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }  
}
