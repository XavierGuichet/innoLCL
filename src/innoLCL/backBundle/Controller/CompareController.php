<?php
// src/innoLCL/backBundle/Controller/CompareController.php

namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use innoLCL\bothIdeaBundle\Entity\Review;

class CompareController extends Controller
{
    public function displayAction($ideaid)
    {
        $twig = array('currentview' => "compare");
        
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        $serviceIdea = $this->container->get('inno_lc_lboth_idea.serviceIdea');        
        $repositoryIdea = $em->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        $repositoryReview = $em->getRepository('innoLCL\bothIdeaBundle\Entity\Review');
        
        // BLOCK DE REDIRECTION DES ANOMALIES
        if($serviceBack->isUserWithTooManyRole($user->getRoles())) {
            //dump("L'utilisateur à trop de role d'administration");
            $route = $this->container->get('router')->generate('fos_admin_user_security_login');
            $response = new RedirectResponse($route);
            return $response;
        }
        
        $userAdminRole = $serviceBack->getAdminRole($user->getRoles());
        $twig['userAdminRole'] = $userAdminRole;
        
         // FIN BLOCK DE REDIRECTION DES ANOMALIES       
        
		$idea = $repositoryIdea->find($ideaid);
		
		if(!$serviceBack->canUserEditThisIdea($userAdminRole,$idea->getStatuts(),$idea->getValidated())) {
			return $this->render('innoLCLbackBundle:List:CantCompare.html.twig');
		}         
         
        $twig['idea'] = $idea;
        
        switch($userAdminRole) {
            case "ROLE_MODERATEUR":
					//Recupère les anciennes reviews
					$previousReviewsList = $repositoryReview->getPreviousList($idea,$user);
					
					//Recupère l'actuelle review, s'il n'y en a pas, crée un object review vide
					$currentReview = $repositoryReview->getCurrent($idea,$user);
					
					if($currentReview === null) {
						$currentReview = new Review();
					}
					$twig['reviewstatus'] = $currentReview->getAvis();
					
                    $form = $this->createFormBuilder($currentReview)
								->setAction($this->generateUrl('innolcl_bothIdea_handleModerateurForm',array("ideaid" => $ideaid,"review" => $currentReview)))
								->add('commentaire', 'textarea',array('label'  => 'Commentaire de présélection',
																										'required' => false,
																										'attr' => array('maxlength' => 255)))
								->add('avis', 'choice', array(
											'choices'  => array('notset' => 'notset', 'maybe' => 'Peut-etre', 'validated' => 'Valider', 'refused' => 'Refuser'),
											'required' => true,
								))
								->add('save', 'submit', array('label' => 'Enregistrer'))
								->add('reset', 'reset', array('label' => 'Annuler'))
								->getForm();
                    
                    $twig['form_view'] = $this->render('innoLCLbothIdeaBundle:Form:moderateur.html.twig', array(
                    'form' => $form->createView(),
                    'currentview' => 'compare',
                    'idea' => $idea,
                    'PreviousReviews' => $previousReviewsList
                ))->getContent();
                return $this->render('innoLCLbackBundle:List:Compare.html.twig',$twig);
            break;
            
            case "ROLE_LECTEUR":
				$twig['reviewstatus'] = $idea->getStatuts();
				$AllReviewsList = $repositoryReview->findBy(array('idea' => $idea),array('versionIdea' => 'asc'));
                $twig['form_view'] = $this->render('innoLCLbothIdeaBundle:Form:lecteur.html.twig', array('idea' => $idea,'currentview' => 'compare','Reviews' => $AllReviewsList))->getContent();
                return $this->render('innoLCLbackBundle:List:Compare.html.twig',$twig);
            break;
            
            case "ROLE_VALIDATEUR" :
				$twig['reviewstatus'] = $idea->getStatuts();
				$AllReviewsList = $repositoryReview->findBy(array('idea' => $idea),array('versionIdea' => 'asc'));
                $form = $this->createFormBuilder($idea)
                    ->setAction($this->generateUrl('innolcl_bothIdea_handleValidateurForm',array("ideaid" => $ideaid)))
                    ->add('statuts', 'choice', array(
                                'choices'  => array('maybe' => 'Peut-etre', 'validated' => 'Valider', 'refused' => 'Refuser'),
                                'required' => true,
                    ))
                    ->add('refusalreason', 'textarea',array('required' => false))
                    ->add('save', 'submit', array('label' => 'Enregistrer'))
                    ->getForm();
                    
                    $twig['form_view']  = $this->render('innoLCLbothIdeaBundle:Form:validateur.html.twig', array(
                    'form' => $form->createView(),
                    'idea' => $idea,
                    'currentview' => 'compare',
                    'Reviews' => $AllReviewsList
                    ))->getContent();
                return $this->render('innoLCLbackBundle:List:Compare.html.twig',$twig);
            break;
            
            case "ROLE_SELECTIONNEUR" :
                $twig['reviewstatus'] = $idea->getStatuts();
				$AllReviewsList = $repositoryReview->findBy(array('idea' => $idea),array('versionIdea' => 'asc'));
                $twig['form_view'] = $this->render('innoLCLbothIdeaBundle:Form:lecteur.html.twig', array('idea' => $idea,'currentview' => 'compare','Reviews' => $AllReviewsList))->getContent();
                return $this->render('innoLCLbackBundle:List:Compare.html.twig',$twig);
            break;    
        }
    }
}
