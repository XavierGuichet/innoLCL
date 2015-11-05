<?php

namespace innoLCL\bothIdeaBundle\Controller;

use innoLCL\bothIdeaBundle\Entity\Idea;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FormController extends Controller
{
    public function createFrontFormAction($author, $ideaID, Request $request)
    {        
        $user = $this->get('security.context')->getToken()->getUser();
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        if($author != $user->getId()) { throw new NotFoundHttpException("Incohérence d'id utilisateur"); }

        if($ideaID !== 0) {
            $newidea = $repositoryIdea->find($ideaID);
            if($newidea === null) {throw new NotFoundHttpException("idée non trouvée");}
            $newidea->setPostedon(new \DateTime());
            $newidea->setAuthor($user);
        }
        else {
            $newidea = new Idea();
        }
        
        $form = $this->createFormBuilder($newidea, ['attr' => ['id' => 'suggest_idea_front', 'autocomplete' => "off" ]])
            ->setAction($this->generateUrl('innolcl_bothIdea_handleFrontForm',array("ideaid" => $ideaID)))
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
            
        return $this->render('innoLCLbothIdeaBundle:Form:front.html.twig', array(
            'form' => $form->createView(),
        ));   
    }
}
