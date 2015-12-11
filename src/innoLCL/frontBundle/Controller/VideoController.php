<?php

namespace innoLCL\frontBundle\Controller;

use innoLCL\Statbundle\Entity\VideoStat;
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
        else { return new JsonResponse(array('message' => 'erreur de validation de la video'),200);}
    }
    public function incrementStatCounterAction($name, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$repositoryVideo = $em->getRepository('innoLCL\StatBundle\Entity\VideoStat');
		$videoStatService = $this->container->get('inno_lcl_stat.video');
		
		if (!$request->isXmlHttpRequest()) {
           return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }     
        
        if($videoStatService->videoExist($name)) {
			$VideoStat = $repositoryVideo->findOneBy(array("videoname" => $name));
			if($VideoStat === null) {
				$VideoStat = new VideoStat;
				$VideoStat->setVideoname($name);
				$VideoStat->setCounter(1);
			}
			else {
				$VideoStat->incrementCounter();
			}
			$em->persist($VideoStat);
            $em->flush();
            
			return new JsonResponse(array('message' => 'Success'), 200);
		}
		
		return new JsonResponse(array('error' => 1,'message' => 'Cette vidéo n\'existe pas.'), 200);		
	}
}
