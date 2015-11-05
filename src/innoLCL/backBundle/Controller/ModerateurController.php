<?php
namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ModerateurController extends Controller
{
    public function ListAction($page) {
        $twig = array('currentview' => 'all');
        
        $user = $this->get('security.context')->getToken()->getUser();
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        $serviceIdea = $this->container->get('inno_lc_lboth_idea.serviceIdea');   
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        
        $ideaList = $repositoryIdea->getListIdeaByStatut('notmoderated',0);
        $twig['idealist'] = $ideaList;
        
        $ideacount['notmoderated']  = $repositoryIdea->getIdeaCountByStatut("moderated",0);
        $twig['ideacount'] = $ideacount;
        
        
        return $this->render('innoLCLbackBundle:List:Moderateur.html.twig',$twig);
        }
}
