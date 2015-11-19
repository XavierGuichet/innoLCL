<?php

namespace innoLCL\bothIdeaBundle\Controller;

use innoLCL\bothIdeaBundle\Entity\Idea;
use innoLCL\bothIdeaBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormError;

class DefaultController extends Controller
{
    public function handleFrontFormAction($ideaid = 0, Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $repositoryIdea = $em->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        if (!$request->isXmlHttpRequest()) {
           return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        
        if($ideaid != 0) {
            $idea = $repositoryIdea->find($ideaid); 
            if($idea === null || $idea->getAuthor()->getId() != $user->getId()) {throw new NotFoundHttpException("idée non trouvée, ou author différents");}
            $idea->setPostedon(new \DateTime());
            $idea->setAuthor($user);
        }
        else {
            $idea = new Idea();
            $idea->setAuthor($user);
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
            ->add('bonuscontent', 'textarea',array('label'  => 'Question facultative : si votre idée était sélectionnée,',
                                                                                    'required' => false,
                                                                                        'attr' => array('maxlength' => 200)))
            ->add('save', 'submit', array('label' => 'Enregistrer'))
            ->getForm();
        
        $form->handleRequest($request);
        
        if($ideaid == 0){
            // on vérifie que ce n'est pas un envoi multiple
            
            $ideas = $repositoryIdea->findAllNotRefusedFor($user->getId());
            
            if(count($ideas)>0){
                
               $form->get('title')->addError(new FormError("Vous ne pouvez pas envoyer de nouvelle idée. Vous en avez déjà une en attente de validation."));
                
                // erreur pas le droit d'écrire une nouvelle si une idée a été trouvée en peut-être/validée ou non modérée
                return new JsonResponse( array('message' => 'Une idée est déjà en attente de validation!',
                                                'form' => $this->renderView('innoLCLbothIdeaBundle:Form:frontraw.html.twig',
                                                array(
                                            'idea' => $idea,
                                            'form' => $form->createView(),
                                        ))), 400);
            }
            
        }
        
        
        
        
        
        if ($form->isValid()) {
            if($idea->sanitize()) { //remove only html tag on varchar actually, named in case there's more to do but Doctrine should do all the work
                $typeMail = "newIdea";
                if($idea->getStatuts() == "maybe" && $idea->getValidated() == 1) {//si l'idée est un rework, reset son statut de moderation actuel, de validation et la fait devenir un rework
                    $idea->setStatuts('notmoderated');
                    $newversion = $idea->getVersion() + 1;
                    $idea->setVersion($newversion);
                    $idea->setValidated(0);
                    $titre = "Merci";
                    $message_popup = "Votre modification a bien été prise en compte.";        
                    $typeMail = "modifyIdea";         
                }
                elseif($idea->getStatuts() == "validated" && $idea->getValidated() == 1) {  // SI l'on veut recup l'info de si une idée est un rework de validé, c'est içi qu'il faudra ajouter le setter
                    $titre = "Merci";
                    $newversion = $idea->getVersion() + 1;
                    $idea->setVersion($newversion);
                    $message_popup = "Votre modification a bien été prise en compte.<br/>Vous serez informé rapidement de la suite à donner de votre idée.";
                    //$typeMail = "improveIdea";
                    $typeMail = "none";
                    }
                else {
                    $titre = "Merci";
                    $newNbideaposted = $user->getNbideaposted() + 1;
                    $user->setNbideaposted($newNbideaposted);
					$em->persist($user);
                    $message_popup = "Votre participation au Challenge de l'innovation a bien été enregistrée.<br/>Vous serez informé rapidement de la suite à donner de votre idée.";
                }
                $reponse = array('message' => 'Success !',
                                                            'succespopup' => $this->renderView('innoLCLbothIdeaBundle:Form:frontsuccess.html.twig',
                                                            array('titre' => $titre,                 
                                                                    'texte' => $message_popup)));
                                                                    
                $em->persist($idea);
                $em->flush();
                if($typeMail != "none") {
                    $to = $idea->getAuthor()->getEmail();
                   if (!$this->get('mail_to_user')->sendEmailIdeeFront($to,$typeMail)) {
                        throw $this->createNotFoundException('Unable to send Idea-front-'.$typeMail.' mail.');
                    }
                }
                
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
        $em = $this->getDoctrine()->getManager();
        
        $repositoryIdea = $em->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        $repositoryReview = $em->getRepository('innoLCL\bothIdeaBundle\Entity\Review');
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        
        if($ideaid != 0) {
            $idea = $repositoryIdea->find($ideaid);              
        }        
        
        if($idea === null) {return new JsonResponse(array('error' => 1,'message' => 'Cette idée n\'existe pas.'), 200);}
        
        if(!$serviceBack->canUserEditThisIdea("ROLE_MODERATEUR",$idea->getStatuts(),$idea->getValidated())) { 
            return new JsonResponse(array('error' => 1,'message' => 'Cette idée vient d\être modéré par le validateur. Votre avis n\'a pas pu être enregistré.'), 200); 
        }
        
		//Recupère les anciennes reviews
		$previousReviewsList = $repositoryReview->getPreviousList($idea,$user);

		//Recupère l'actuelle review, s'il n'y en a pas, crée un object review vide
		$currentReview = $repositoryReview->getCurrent($idea,$user);
        if($currentReview === null) {
						$currentReview = new Review();
		}
        
        $form = $this->createFormBuilder($currentReview)
            ->setAction($this->generateUrl('innolcl_bothIdea_handleModerateurForm',array("ideaid" => $ideaid)))
            ->add('commentaire', 'textarea',array('label'  => 'Commentaire de présélection',
                                                                                    'required' => false,
                                                                                    'attr' => array('maxlength' => 255)))
            ->add('avis', 'choice', array(
                        'choices'  => array('maybe' => 'Peut-etre', 'validated' => 'Valider', 'refused' => 'Refuser'),
                        'required' => true,
            ))
            ->add('save', 'submit', array('label' => 'Enregistrer'))
            ->add('reset', 'reset', array('label' => 'Annuler'))
            ->getForm();
        
        
         $form->handleRequest($request);
         
         
         if ($form->isValid()) {
            $currentReview->setModerateur($user);
            $currentReview->setIdea($idea);
            $currentReview->setVersionIdea($idea->getVersion());
            $em->persist($currentReview);
            if($idea->sanitize()) { //remove only html tag on varchar actually, named in case there's more to do but Doctrine should do all the work               
                $em->flush();
                return new JsonResponse(array('error' => 0,'message' => 'Success!'), 200);
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
                                                        'PreviousReviews' => $previousReviewsList,
                                                    ))), 400);
          return $response;
        }

    }
    
    public function handleValidateurFormAction($ideaid = 0, Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $repositoryIdea = $em->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        
        if($ideaid != 0) {
            $idea = $repositoryIdea->find($ideaid);              
        }        
        
        if($idea === null) {return new JsonResponse(array('error' => 1,'message' => 'Cette idée n\'existe pas.'), 200);}
        
        if(!$serviceBack->canUserEditThisIdea("ROLE_VALIDATEUR",$idea->getStatuts(),$idea->getValidated())) { 
            return new JsonResponse(array('error' => 1,'message' => 'Cette idée est déjà modérée.'), 200); 
        }
        
        $form = $this->createFormBuilder($idea)
                    ->setAction($this->generateUrl('innolcl_bothIdea_handleValidateurForm',array("ideaid" => $ideaid)))
                    ->add('statuts', 'choice', array(
                                'choices'  => array('maybe' => 'Peut-etre', 'validated' => 'Valider', 'refused' => 'Refuser'),
                                'required' => true,
                    ))
                    ->add('refusalreason', 'textarea',array('required' => false))
                    ->add('save', 'submit', array('label' => 'Enregistrer'))
                    ->add('compare', 'button', array('label' => 'Comparer'))
                    ->getForm();
        
         $form->handleRequest($request);
         
         
         if ($form->isValid()) {
            $em->persist($idea);
            if($idea->sanitize()) { //remove only html tag on varchar actually, named in case there's more to do but Doctrine should do all the work
                $idea->setValidated(1);          
                $em->flush();
                
                //send mail HERE
                if($idea->getValidated()){
                    $to = $idea->getAuthor()->getEmail();
                    $motif = $idea->getRefusalreason();
                    if($idea->getStatuts() == "validated"){
                        if (!$this->get('mail_to_user')->sendEmailValider($to)) {
                            throw $this->createNotFoundException('Unable to send Idea-valider mail.');
                        }
                    }
                    elseif($idea->getStatuts() == "refused"){
                        if (!$this->get('mail_to_user')->sendEmailRefuser($to, $motif)) {
                            throw $this->createNotFoundException('Unable to send Idea-refuser mail.');
                        }
                    }
                    elseif($idea->getStatuts() == "maybe"){
                        if (!$this->get('mail_to_user')->sendEmailPeutEtre($to)) {
                            throw $this->createNotFoundException('Unable to send Idea-peut-etre mail.');
                        }
                    }
                }
                
                return new JsonResponse(array('error' => 0,'message' => 'Success!'), 200);
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
    
    public function handleSelectionneurFormAction($ideaid = 0, Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $repositoryIdea = $em->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        
        if($ideaid != 0) {
            $idea = $repositoryIdea->find($ideaid);              
        }        
        
        if($idea === null) {return new JsonResponse(array('error' => 1,'message' => 'Cette idée n\'existe pas.'), 200);}
        
        if(!$serviceBack->canUserEditThisIdea("ROLE_SELECTIONNEUR",$idea->getStatuts(),$idea->getValidated())) { 
            return new JsonResponse(array('error' => 1,'message' => 'Cette idée est déjà modérée.'), 200); 
        }
        
        if($idea->getSelected() == 1) {
            $idea->setSelected(0);
        }
        elseif($idea->getSelected() == 0) {
            $idea->setSelected(1);
        }
        $em->persist($idea);
        $em->flush();
        return new JsonResponse(array('error' => 0,'message' => 'Success!'), 200);

    }
    
    public function handleSelectionneurFinaliseFormAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $repositoryIdea = $em->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }
        
        if($repositoryIdea->getSelectedIdeaCount() == 10) {
            foreach ($repositoryIdea->findBySelected(1) as $idee) {
                $idee->setSelected(2);
                $em->persist($idee);
            }
            $em->flush();
            return new JsonResponse(array('error' => 0,'message' => 'C\'est validé'), 200); 
        }
        else {
            return new JsonResponse(array('error' => 1,'message' => 'La base de donnée ne contient pas 10 idées sélectionnées. Veuillez recharger la page.'), 200); 
        }

    }
}
