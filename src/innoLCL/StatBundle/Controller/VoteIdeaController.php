<?php

namespace innoLCL\StatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use innoLCL\StatBundle\Entity\Votes;

class VoteIdeaController extends Controller
{
    public function voteAction($idealaureatid = 0, Request $request)
    {
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $repoVote = $em->getRepository('innoLCL\StatBundle\Entity\Votes');
        $repoIdeaLaureat = $em->getRepository('innoLCL\bothIdeaBundle\Entity\IdeaLaureat');

        if(!$request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
        }

        if(!($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            return new JsonResponse(array('content' => 'Vous devez être connecté pour voter'), 200);
        }

        //Recupère le dernier vote de l'utilisateur
        $lastVote = $repoVote->findLastVoteByUser($user);
        //Si a dejà voter et que ce vote date d'aujourd'hui
        if($lastVote && strtotime(date("Y-m-d")) == $lastVote->getDateVote()->getTimestamp()) {
            return new JsonResponse(array('content' => 'Vous avez déjà voté aujourd\'hui. Revenez demain pour voter et soutenir vos idées préférées.'), 200);
        }

        //Ajout du vote
        $currentVote = new Votes();
        $currentVote->setUser($user);
        $IdeaVotedFor = $repoIdeaLaureat->find($idealaureatid);
        $currentVote->setIdeaLaureat($IdeaVotedFor);
        $em->persist($currentVote);

        //Mise a jour des votes au niveau des idees
        $totalVotes = $repoVote->getTotalVoteCount() + 1; //+1 pour ajout du vote en cours
        $IdeaLaureats = $repoIdeaLaureat->findAll();
        foreach($IdeaLaureats as $IdeaLaureat) {
            if($IdeaLaureat->getId() == $idealaureatid) {
                $new_nb_vote = $IdeaLaureat->getNbVotes() + 1;
                $IdeaLaureat->setNbVotes($new_nb_vote);
            }
            $new_pourcent = $IdeaLaureat->getNbVotes() / $totalVotes * 100;
            $IdeaLaureat->setPourcVotes(round($new_pourcent,2));
            $em->persist($IdeaLaureat);
        }

        $em->flush();

        return new JsonResponse(array('content' => 'Votre vote a bien été pris en compte. Revenez demain pour voter et soutenir vos idées préférées.'), 200);
    }
}
