<?php
namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ValidateurController extends Controller
{
        public function ListAction($statut,$page) {
            $twig = array('currentview' => $statut);
            
            $user = $this->get('security.context')->getToken()->getUser();
            $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
            $serviceIdea = $this->container->get('inno_lc_lboth_idea.serviceIdea');   
            $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
            
            
            if($statut == "all") { $statut = 'notmoderated';$not = true;}
            else{$not = false;}
            $ideaList = $repositoryIdea->getListIdeaByStatut($statut,0,$not);
            $twig['idealist'] = $ideaList;
            
            $ideacount['all']  = $repositoryIdea->getIdeaCountByStatut('notmoderated',0,true);
            $ideacount['maybe']  =  $repositoryIdea->getIdeaCountByStatut('maybe',0);
            $ideacount['refused']  =  $repositoryIdea->getIdeaCountByStatut('refused',0);
            $ideacount['validated']  =  $repositoryIdea->getIdeaCountByStatut('validated',0);
            $twig['ideacount'] = $ideacount;
            
            
            return $this->render('innoLCLbackBundle:List:Validateur.html.twig',$twig);
        }
        
}
