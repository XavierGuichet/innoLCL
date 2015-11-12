<?php
namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SelectionneurController extends Controller
{
        public function ListAction($page) {
            $twig = array('currentview' => "validated");
            $user = $this->get('security.context')->getToken()->getUser();
            $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
            $serviceIdea = $this->container->get('inno_lc_lboth_idea.serviceIdea');   
            $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
            
            
            $ideaList = $repositoryIdea->getListIdeaByStatut("validated",1,false,0);
            $twig['idealist'] = $ideaList;
            
            $twig['ideacountvalidated']  = $repositoryIdea->getIdeaCountByStatut("validated",1);
            $twig['ideacountselected'] = 10 - $repositoryIdea->getSelectedIdeaCount();
            if($repositoryIdea->getSelectedIdeaBlockedCount() == 10) {
                $twig['selectedconfirmed'] = 1;
            }
            else { $twig['selectedconfirmed'] = 0;}
            //$twig['selectedconfirmed'] = 1;
            return $this->render('innoLCLbackBundle:List:Selectionneur.html.twig',$twig);
        }        
}
