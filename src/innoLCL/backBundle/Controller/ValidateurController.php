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
            
            
            if($statut == "all") { 
				$not = true;
				$ideaList = $repositoryIdea->getLecteurValidateurListIdea($page);
			}
			else{
				$not = false;
				$ideaList = $repositoryIdea->getListIdeaByStatut($statut,1,$not,$page);
			}
            $twig['idealist'] = $ideaList;
            
            $ideacount['all']  = $repositoryIdea->getLecteurValidateurListIdeaCount();
			$ideacount['maybe']  =  $repositoryIdea->getIdeaCountByStatut('maybe',1);
			$ideacount['refused']  =  $repositoryIdea->getIdeaCountByStatut('refused',1);
			$ideacount['validated']  =  $repositoryIdea->getIdeaCountByStatut('validated',1);
			$twig['ideacount'] = $ideacount;
            
			$twig['nb_page'] = ceil($ideacount[$statut] / 15);
			$twig['current_page'] = $page;
        
            
            return $this->render('innoLCLbackBundle:List:Validateur.html.twig',$twig);
        }
        
}
