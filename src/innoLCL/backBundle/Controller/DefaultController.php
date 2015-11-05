<?php

namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function allAction($viewstatuts, Request $request)
    {
        $twig = array('currentview' => $viewstatuts);
        
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
        
        if($serviceBack->canRoleViewThisList($viewstatuts,$userAdminRole) === false) {
            dump("L'utilisateur n'a pas les droits sur cette liste");
            $defaultViewOfRole = $serviceBack->defaultViewOfRole($userAdminRole);
            $route = $this->container->get('router')->generate('innolcl_back_view', array('viewstatuts' => $defaultViewOfRole));
            $response = new RedirectResponse($route);
            return $response;
        }
        // FIN BLOCK DE REDIRECTION DES ANOMALIES
        
        $userSeeModeratedOnly = $serviceBack->canUserModeratedIdeaOnly($userAdminRole);
        $userSeeValidatedOnly = $serviceBack->canUserValidatedIdeaOnly($userAdminRole);
        
        
        $ideacount['notmoderated']  = $repositoryIdea->getIdeaCountByStatut("notmoderated",$userSeeModeratedOnly);
        $ideacount['maybe']  =  $repositoryIdea->getIdeaCountByStatut("maybe",$userSeeModeratedOnly);
        $ideacount['refused']  =  $repositoryIdea->getIdeaCountByStatut("refused",$userSeeModeratedOnly);
        $ideacount['validated']  =  $repositoryIdea->getIdeaCountByStatut("validated",$userSeeModeratedOnly);
        $twig['ideacount'] = $ideacount;
        
        $ideaList = $repositoryIdea->findBy(
                        array('statuts' => 'notmoderated'), // Critere
                        array('id' => 'asc')
            );
        $twig['idealist'] = $ideaList;
        
        switch($userAdminRole) {
            case "ROLE_MODERATEUR":
                return $this->render('innoLCLbothIdeaBundle:IdeaList:moderateur_all.html.twig',$twig);
            break;
            case "ROLE_LECTEUR":
                return $this->render('innoLCLbothIdeaBundle:IdeaList:moderateur_'.$viewstatuts.'.html.twig',$twig);
            break;
        }
    }
    public function getIdeaAction($ideaid) {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        if(is_string($user)) { return new JsonResponse(array('error' => 'unauth'), 200);}
        $role = $serviceBack->getAdminRole($user->getRoles());

        if($ideaid != 0) {
            $idea = $repositoryIdea->find($ideaid);
        }
        else{return new JsonResponse(array('message' => 'Cette idée n\'existe pas.'), 200);  }
        
        if(!$serviceBack->canUserEditThisIdea($role,$idea->getStatuts(),$idea->getValidated())) { 
            return new JsonResponse(array('message' => 'Vous n\'aves pas les droits sur cette idée, un collaborateur a déjà du la traiter !'), 200); 
        }
        
        $twig['idea'] = $idea;
        
        switch($role) {
            case "ROLE_MODERATEUR" :
                    $form = $this->createFormBuilder($idea)
                    ->setAction($this->generateUrl('innolcl_bothIdea_handleModerateurForm',array("ideaid" => $ideaid)))
                    ->add('commentary', 'textarea',array('label'  => 'Commentaire de présélection',
                                                                                            'required' => false,
                                                                                            'attr' => array('maxlength' => 255)))
                    ->add('statuts', 'choice', array(
                                'choices'  => array('maybe' => 'Peut-etre', 'validated' => 'Valider', 'refused' => 'Refuser'),
                                'required' => true,
                    ))
                    ->add('save', 'submit', array('label' => 'Enregistrer'))
                    ->add('reset', 'reset', array('label' => 'Annuler'))
                    ->add('compare', 'button', array('label' => 'Comparer'))
                    ->getForm();
                    
                    $form_view = $this->render('innoLCLbothIdeaBundle:Form:moderateur.html.twig', array(
                    'form' => $form->createView(),
                    'idea' => $idea
                ));
                return new JsonResponse(array( 'message' => '','HTMLcontent' => $form_view->getContent()), 200);
            break;
            case "ROLE_LECTEUR" :
                $view = $this->render('innoLCLbothIdeaBundle:Form:lecteur.html.twig', array('idea' => $idea));
                return new JsonResponse(array( 'message' => '','HTMLcontent' => $view->getContent()), 200);
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
                    ->add('compare', 'button', array('label' => 'Comparer'))
                    ->getForm();
                    
                    $form_view = $this->render('innoLCLbothIdeaBundle:Form:validateur.html.twig', array(
                    'form' => $form->createView(),
                    'idea' => $idea
                ));
                 return new JsonResponse(array( 'message' => '','HTMLcontent' => $form_view->getContent()), 200);
            break;
            case "ROLE_SELECTIONNEUR" :
            
            break;    
        }
        
        return new JsonResponse(array('message' => 'error X'), 400);        
    }
}
