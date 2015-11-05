<?php

namespace innoLCL\frontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function indexAction(Request $request) // /accueil
    {
        $securityContext = $this->get('security.context');
        
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
            return new RedirectResponse($route);
        }
        
        return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
    
    public function proposalAction(Request $request) // phase 1
    {
        $repositoryIdea = $this->getDoctrine()->getManager()->getRepository('innoLCL\bothIdeaBundle\Entity\Idea');
        
        $user = $this->get('security.context')->getToken()->getUser();
        $userId = $user->getId();
                
        $twig = array('authorid' => "$userId",
                              'displayCTA' => 0,
                              'displayForm' => 0,
                              'FormIdeaID' => 0,
                              'idealist' => array()); //valeur par défault pour twig
               
         $twig['urladmin'] = $this->get('router')->generate('innolcl_moderateur_list_idea',array('page' => 1));
        
        
        //Verification du role => admin ejecté ou limité ? Si les admin ne peuvent pas participer ? ou alors incapacité d'afficher le CTA
        
        if($repositoryIdea->getCountIdeaOfUser($userId) > 0) //Si l'user déjà des idées => affiche la liste d'idée
        {
            $twig['idealist'] = $repositoryIdea->findBy(array('author' => $userId),array('postedon' => 'desc'));
        }
        
        //Defini si le CTA et le formulaire sera affiché / disponible
        if($user->getVideoseenon() != null)
        {
            $twig['displayForm'] = 1;
            //Defini si le form pour soumettre une nouvelle idée est affiché (les cas d'idea 'peut-etre' et 'valider' s'affiche directement grâce au twig)
            if($repositoryIdea->isUserLastIdeaRefused($userId) || $repositoryIdea->getCountIdeaOfUser($userId) == 0) // Si dernière idée réfusé ou pas d'idée déposé par cet user
            {
                $twig['displayCTA'] = 1;
                $twig['FormIdeaID'] = 0;
            }
            else 
            {
                $twig['displayCTA'] = 0;
                $twig['FormIdeaID'] = $repositoryIdea->getLastIdeaIdOfUser($userId);
            }
        }
        
        
          return $this->render('innoLCLfrontBundle:Default:proposal.html.twig',$twig);
    }
    
    public function SelectionAction() // phase 2
    {
         //return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
    
    public function laureatAction() // phase 3
    {
         //return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
    
    public function voteAction() // phase 4
    {
         //return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
    
    public function  resultsAction() // phase 5
    {
         //return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
}
