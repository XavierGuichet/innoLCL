<?php
namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LecteurController extends Controller
{
    public function ListAction($statut,$page) {        
        $twig = array('currentview' => $statut);
        
        $user = $this->get('security.context')->getToken()->getUser();
        $serviceBack = $this->container->get('inno_lc_lback.serviceBack');  
        $serviceIdea = $this->container->get('inno_lc_lboth_idea.serviceIdea');   
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        
        if($statut == "all") { $statut = 'notmoderated';$not = true;}
        else{$not = false;}
        $ideaList = $repositoryIdea->getListIdeaByStatut($statut,0,$not,$page);
        $twig['idealist'] = $ideaList;
        
        $ideacount['all']  = $repositoryIdea->getIdeaCountByStatut('notmoderated',0,true);
        $ideacount['maybe']  =  $repositoryIdea->getIdeaCountByStatut('maybe',0);
        $ideacount['refused']  =  $repositoryIdea->getIdeaCountByStatut('refused',0);
        $ideacount['validated']  =  $repositoryIdea->getIdeaCountByStatut('validated',0);
        $twig['ideacount'] = $ideacount;
        
        $twig['nb_page'] = ceil($ideacount[$statut] / 15);
        $twig['current_page'] = $page;
        
        
        return $this->render('innoLCLbackBundle:List:Lecteur.html.twig',$twig);
        }
        
}
