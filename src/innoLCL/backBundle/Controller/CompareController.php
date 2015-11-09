<?php
// src/innoLCL/backBundle/Controller/CompareController.php

namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CompareController extends Controller
{
    public function displayAction($ideaid)
    {
        $twig = array('currentview' => "compare");
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        $serviceIdea = $this->container->get('inno_lc_lboth_idea.serviceIdea');        
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        // BLOCK DE REDIRECTION DES ANOMALIES
        if($serviceBack->isUserWithTooManyRole($user->getRoles())) {
            dump("L'utilisateur à trop de role d'administration");
            $route = $this->container->get('router')->generate('fos_admin_user_security_login');
            $response = new RedirectResponse($route);
            return $response;
        }
        
        $userAdminRole = $serviceBack->getAdminRole($user->getRoles());
        $twig['userAdminRole'] = $userAdminRole;
        
         // FIN BLOCK DE REDIRECTION DES ANOMALIES       
        
       
        $userSeeModeratedOnly = $serviceBack->canUserModeratedIdeaOnly($userAdminRole);
        $userSeeValidatedOnly = $serviceBack->canUserValidatedIdeaOnly($userAdminRole);
        
        
        
        
        $idea = $repositoryIdea->find($ideaid);


         if(!$serviceBack->canUserEditThisIdea($userAdminRole,$idea->getStatuts(),$idea->getValidated())) {
                return $this->render('innoLCLbackBundle:List:CantCompare.html.twig');
         }
         
         
        $twig['idea'] = $idea;
        
        switch($userAdminRole) {
            case "ROLE_MODERATEUR":
                $form = $this->createFormBuilder($idea)
                    ->setAction($this->generateUrl('innolcl_bothIdea_handleModerateurForm',array("ideaid" => $ideaid)))
                    ->add('commentary', 'textarea',array('label'  => 'Commentaire de présélection',
                                                                                            'required' => false,
                                                                                            'attr' => array('maxlength' => 255)))
                    ->add('statuts', 'choice', array(
                                'choices'  => array('notset' => 'notset', 'maybe' => 'Peut-etre', 'validated' => 'Valider', 'refused' => 'Refuser'),
                                'required' => true,
                    ))
                    ->add('save', 'submit', array('label' => 'Enregistrer'))
                    ->add('reset', 'reset', array('label' => 'Annuler'))
                    ->getForm();
                    
                    $twig['form_view'] = $this->render('innoLCLbothIdeaBundle:Form:moderateur.html.twig', array(
                    'form' => $form->createView(),
                    'idea' => $idea
                ))->getContent();
            
            
            
                return $this->render('innoLCLbackBundle:List:Compare.html.twig',$twig);
            break;
            case "ROLE_LECTEUR":
                $twig['form_view'] = $this->render('innoLCLbothIdeaBundle:Form:lecteur.html.twig', array('idea' => $idea))->getContent();;
                return $this->render('innoLCLbackBundle:List:Compare.html.twig',$twig);
            break;
            case "ROLE_VALIDATEUR" :
                $form = $this->createFormBuilder($idea)
                    ->setAction($this->generateUrl('innolcl_bothIdea_handleValidateurForm',array("ideaid" => $ideaid)))
                    ->add('statuts', 'choice', array(
                                'choices'  => array('maybe' => 'Peut-etre', 'validated' => 'Valider', 'refused' => 'Refuser'),
                                'required' => true,
                    ))
                    ->add('refusalreason', 'hidden')
                    ->add('save', 'submit', array('label' => 'Enregistrer'))
                    ->add('reset', 'reset', array('label' => 'Annuler'))
                    ->getForm();
                    
                    $twig['form_view']  = $this->render('innoLCLbothIdeaBundle:Form:validateur.html.twig', array(
                    'form' => $form->createView(),
                    'idea' => $idea
                    ))->getContent();
                return $this->render('innoLCLbackBundle:List:Compare.html.twig',$twig);
            break;
            case "ROLE_SELECTIONNEUR" :
                $twig['form_view'] = $this->render('innoLCLbothIdeaBundle:Form:lecteur.html.twig', array('idea' => $idea))->getContent();;
                return $this->render('innoLCLbackBundle:List:Compare.html.twig',$twig);
            break;    
        }
    }
}
