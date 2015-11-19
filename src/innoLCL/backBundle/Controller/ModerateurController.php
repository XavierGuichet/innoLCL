<?php
namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ModerateurController extends Controller
{
    public function ListAction($statut,$page) {
        $twig = array('currentview' => $statut);
        
        $user = $this->get('security.context')->getToken()->getUser();
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        $serviceIdea = $this->container->get('inno_lc_lboth_idea.serviceIdea');   
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        if($statut == 'all') {
			$statut = 'notmoderated';
			$ideaList = $repositoryIdea->getModerateurListIdea($user,false,$page);
		}
		else {
			$ideaList = $repositoryIdea->getModerateurListIdea($user,$statut,$page);
		}
		
		$twig['idealist'] = $ideaList;
		
		$ideacount['notmoderated']  = $repositoryIdea->getModerateurListIdeaCount($user);
        $ideacount['maybe']  = $repositoryIdea->getModerateurListIdeaCount($user,'maybe');
        $ideacount['validated']  = $repositoryIdea->getModerateurListIdeaCount($user,'validated');
        $ideacount['refused']  = $repositoryIdea->getModerateurListIdeaCount($user,'refused');
        
        $twig['ideacount'] = $ideacount;
        $twig['nb_page'] = ceil($ideacount[$statut] / 15);
        $twig['current_page'] = $page;
        
        return $this->render('innoLCLbackBundle:List:Moderateur.html.twig',$twig);
        }
}
