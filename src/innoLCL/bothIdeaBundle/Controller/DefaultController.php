<?php

namespace innoLCL\bothIdeaBundle\Controller;

use innoLCL\bothIdeaBundle\Entity\Idea;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    public function handleFrontFormAction($ideaid = 0, Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        /*if (!$request->isXmlHttpRequest()) {
           return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }*/
        
        if($ideaid != 0) {
            $idea = $repositoryIdea->find($ideaid); 
            if($idea === null || $idea->getAuthor()->getId() != $user->getId()) {throw new NotFoundHttpException("idée non trouvée, ou author différents");}
            $idea->setPostedon(new \DateTime());
            $idea->setAuthor($user);
        }
        else {
            $idea = new Idea();
        }
        
        $form = $this->createFormBuilder($idea, ['attr' => ['id' => 'suggest_idea_front', 'autocomplete' => "off" ]])
                    ->setAction($this->generateUrl('innolcl_bothIdea_handleFrontForm',array("ideaid" => $ideaid)))
            ->add('title', 'text',array('label'  => 'Titre de votre idée',
                                                  'attr' => array('maxlength' => 125)))
            ->add('description', 'textarea',array('label'  => 'Descriptif synthétique de votre idée',
                                                                                        'attr' => array('maxlength' => 200)))
            ->add('customerprofit', 'textarea',array('label'  => 'Descriptif du bénéfice de votre idée pour le client',
                                                                                        'required' => false,
                                                                                        'attr' => array('maxlength' => 200)))
            ->add('partnerprofit', 'textarea',array('label'  => 'Descriptif du bénéfice de votre idée pour les collaborateurs',
                                                                                        'required' => false,
                                                                                        'attr' => array('maxlength' => 200)))
            ->add('bonuscontent', 'textarea',array('label'  => 'Question bonus : si votre idée était sélectionnée,',
                                                                                    'required' => false,
                                                                                        'attr' => array('maxlength' => 200)))
            ->add('save', 'submit', array('label' => 'Enregistrer'))
            ->getForm();
        
        $form->handleRequest($request);
        $idea->setAuthor($user);
        
        
        
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($idea);
            if($idea->sanitize()) { //remove only html tag on varchar actually, named in case there's more to do but Doctrine should do all the work
                if($idea->getStatuts() == "maybe" && $idea->getValidated() == 1) {//si l'idée est un rework, reset son statut de moderation actuel, de validation et la fait devenir un rework
                    $idea->setStatuts('notmoderated');
                    $idea->setReworked(1);
                    $idea->setValidated(0);
                    $titre = "Merci";
                    $message_popup = "Votre modification a bien été prise en compte.";                 
                }
                elseif($idea->getStatuts() == "validated" && $idea->getValidated == 1) {  // SI l'on veut recup l'info de si une idée est un rework de validé, c'est içi qu'il faudra ajouter le setter
                    $titre = "Merci";
                    $message_popup = "Votre modification a bien été prise en compte.<br/>Vous serez informé rapidement de la suite à donner de votre idée.";
                    }
                else {
                    $titre = "Merci";
                    $message_popup = "Votre participation au Challenge de l'innovation a bien été enregistrée.<br/>Vous serez informé rapidement de la suite à donner de votre idée.";
                }
                $reponse = array('message' => 'Success !',
                                                            'succespopup' => $this->renderView('innoLCLbothIdeaBundle:Form:frontsuccess.html.twig',
                                                            array('titre' => $titre,
                                                                    'texte' => $message_popup)));
                $em->flush();
                return new JsonResponse($reponse,200);
            }
            else {
                return new JsonResponse( array('message' => 'Erreur Sanitize!',
                                                            'form' => $this->renderView('innoLCLbothIdeaBundle:Form:frontraw.html.twig',
                                                            array(
                                                        'idea' => $idea,
                                                        'form' => $form->createView(),
                                                    ))), 400);
            }
        }
        else
        {
            return new JsonResponse( array('message' => 'Cas inattendu!',
                                                            'form' => $this->renderView('innoLCLbothIdeaBundle:Form:frontraw.html.twig',
                                                            array(
                                                        'idea' => $idea,
                                                        'form' => $form->createView(),
                                                    ))), 400);
        }
        
        return new JsonResponse( array('message' => 'Cas inattendu!',
                                                            'form' => $this->renderView('innoLCLbothIdeaBundle:Form:frontraw.html.twig',
                                                            array(
                                                        'idea' => $idea,
                                                        'form' => $form->createView(),
                                                    ))), 200);
    }
    
    public function handleModerateurFormAction($ideaid = 0, Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        
        //if (!$request->isXmlHttpRequest()) {
        //    return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        //}
        
        if($ideaid != 0) {
            $idea = $repositoryIdea->find($ideaid);              
        }        
        
        if($idea === null) {return new JsonResponse(array('message' => 'Cette idée n\'existe pas.'), 400);}
        
        if(!$serviceBack->canUserEditThisIdea("ROLE_MODERATEUR",$idea->getStatuts(),$idea->getValidated())) { 
            return new JsonResponse(array('message' => 'Cette idée est déjà modérée. Réactualisez votre page.'), 400); 
        }
        
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
        
        
         $form->handleRequest($request);
         
         
         if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($idea);
            if($idea->sanitize()) { //remove only html tag on varchar actually, named in case there's more to do but Doctrine should do all the work               
                $em->flush();
                return new JsonResponse(array('message' => 'Success!'), 200);
            }
            else {
                return new JsonResponse(array('message' => 'Erreur de sanitization!'), 400);
            }
        }
        else {       
           return new JsonResponse( array('message' => 'Error',
                                                            'form' => $this->renderView('innoLCLbothIdeaBundle:Form:moderateur.html.twig',
                                                            array(
                                                        'idea' => $idea,
                                                        'form' => $form->createView(),
                                                    ))), 400);
          return $response;
        }

    }
    
    public function handleValidateurFormAction($ideaid = 0, Request $request) //SWIFT MAILER HERE
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        
        //if (!$request->isXmlHttpRequest()) {
        //    return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        //}
        
        if($ideaid != 0) {
            $idea = $repositoryIdea->find($ideaid);              
        }        
        
        if($idea === null) {return new JsonResponse(array('message' => 'Cette idée n\'existe pas.'), 400);}
        
        if(!$serviceBack->canUserEditThisIdea("ROLE_VALIDATEUR",$idea->getStatuts(),$idea->getValidated())) { 
            return new JsonResponse(array('message' => 'Cette idée est déjà modérée. Réactualisez votre page.'), 400); 
        }
        
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
        
         $form->handleRequest($request);
         
         
         if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($idea);
            if($idea->sanitize()) { //remove only html tag on varchar actually, named in case there's more to do but Doctrine should do all the work
                $idea->setValidated(1);          
                $em->flush();
                
                //send mail HERE
                return new JsonResponse(array('message' => 'Success!'), 200);
            }
            else {
                return new JsonResponse(array('message' => 'Erreur de sanitization!'), 400);
            }
        }
        else {       
           return new JsonResponse( array('message' => 'Error',
                                                            'form' => $this->renderView('innoLCLbothIdeaBundle:Form:validateur.html.twig',
                                                            array(
                                                        'idea' => $idea,
                                                        'form' => $form->createView(),
                                                    ))), 400);
        }
    }
}
