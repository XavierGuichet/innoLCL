<?php

namespace innoLCL\frontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class VideoController extends Controller
{
    public function createFormAction() {
        $user = $this->get('security.context')->getToken()->getUser();     
        
        if($user->getvideoseenon() == false) {
             $user->setVideoseenon((bool) true);
        $form = $this->createFormBuilder($user, ['attr' => ['id' => 'js-videoform']])
            ->setAction($this->generateUrl('innolcl_frontbundle_videohandleFormAction',array("user" => $user)))
            ->add('videoseenon','checkbox')
            ->add('save', 'submit', array('label' => 'Enregistrer'))
            ->getForm();
        
        return $this->render('innoLCLfrontBundle:Video:videoseen.html.twig', array(
            'form' => $form->createView(),
        ));
        }
        else { return new Response();}
    }
        
        
    public function handleFormAction(Request $request) {
        $user = $this->get('security.context')->getToken()->getUser();
                
        $form = $this->createFormBuilder($user, ['attr' => ['id' => 'js-videoform']])
            ->setAction($this->generateUrl('innolcl_frontbundle_videohandleFormAction',array("user" => $user)))
            ->add('videoseenon','checkbox')
            ->add('save', 'submit', array('label' => 'Enregistrer'))
            ->getForm();            
        
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse(array('message' => 'ok'),200);
        }
        else { return new JsonResponse(array('message' => 'erreur de validation de la video'),400);}
    }
}
