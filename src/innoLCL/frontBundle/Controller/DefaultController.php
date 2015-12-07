<?php

namespace innoLCL\frontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction(Request $request) // /accueil
    {
        $securityContext = $this->get('security.context');
		$currentTime = time();
		$phase2Time = strtotime($this->container->getParameter('phase.phase2'));
		$phase3Time = strtotime($this->container->getParameter('phase.phase3'));
		$phase4Time = strtotime($this->container->getParameter('phase.phase4'));
		$phase5Time = strtotime($this->container->getParameter('phase.phase5'));
        
        if($currentTime < $phase2Time) { //PHASE 1
			if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
				$route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
				return new RedirectResponse($route);
			}
			else {
				return $this->render('innoLCLfrontBundle:Default:index.html.twig');
			}
		}
		elseif($currentTime < $phase3Time) { //PHASE 2
			$route = $this->container->get('router')->generate('innolcl_front_landing_selection');
		}
		elseif($currentTime < $phase4Time) { //PHASE 3
			$route = $this->container->get('router')->generate('innolcl_front_landing_laureat');
		}
		elseif($currentTime >= $phase5Time) { //PHASE 4
			/*if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
				$route = $this->container->get('router')->generate('innolcl_front_landing_vote');
			}
			else {
				return $this->render('innoLCLfrontBundle:Default:index.html.twig');
			}*/
		}
		else { //PHASE 5
			//$route = $this->container->get('router')->generate('innolcl_front_landing_results');
		}
        
        if(isset($route)) {
			return new RedirectResponse($route);
		}
        
        return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
    
    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        //if (!is_object($user) || !$user instanceof UserInterface) {
        /*if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }*/
        if (is_object($user)) {
            return $this->homeConnected($request,true);
        }else{
            // pas d'utilisateur ayant ce token
            // ou token déjà validé et utilisateur abusant du lien de confirmation pour revenir à chaque fois.
            $route = $this->container->get('router')->generate('innolcl_front_homepage');
            return new RedirectResponse($route);
        }
    }
    
    public function proposalAction(Request $request) // phase 1
    {
		/*$currentTime = time();
		$phase2Time = strtotime($this->container->getParameter('phase.phase2'));
		if($currentTime > $phase2Time) {
			return $this->indexAction($request);
		}*/
        return $this->homeConnected($request, false);
    }
    
    private function homeConnected(Request $request, $registerConfirmed)
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
        
        $twig['registerConfirmed'] = $registerConfirmed;
        
          return $this->render('innoLCLfrontBundle:Default:proposal.html.twig',$twig);
    }
    
    
    
    
    public function SelectionAction(Request $request) // phase 2
    {
		$currentTime = time();
		$phase2Time = strtotime($this->container->getParameter('phase.phase2'));
		$phase3Time = strtotime($this->container->getParameter('phase.phase3'));
		if(!($currentTime > $phase2Time && $currentTime < $phase3Time)) {			
			$route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
			return new RedirectResponse($route);
		}
         return $this->render('innoLCLfrontBundle:Default:selection.html.twig');
    }
    
    public function laureatAction(Request $request) // phase 3
    {
		$currentTime = time();
		$phase3Time = strtotime($this->container->getParameter('phase.phase3'));
		$phase4Time = strtotime($this->container->getParameter('phase.phase4'));
		if(!($currentTime > $phase3Time && $currentTime < $phase4Time)) {			
			$route = $this->container->get('router')->generate('innolcl_front_landing_proposal');
			return new RedirectResponse($route);
		}
         return $this->render('innoLCLfrontBundle:Default:laureats.html.twig');
    }
    
    public function voteAction(Request $request) // phase 4
    {
         //return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
    
    public function  resultsAction(Request $request) // phase 5
    {
         //return $this->render('innoLCLfrontBundle:Default:index.html.twig');
    }
}
