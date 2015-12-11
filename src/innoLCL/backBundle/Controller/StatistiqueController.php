<?php
namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatistiqueController extends Controller
{
        public function videoAction() {
			$twig = array('currentview' => "all");
			$em = $this->getDoctrine()->getManager();
			$repositoryVideo = $em->getRepository('innoLCL\StatBundle\Entity\VideoStat');
			
			$videos = $repositoryVideo->findAll();
			
			$twig['videoslist'] = $videos;
			
			$twig['totalview'] = 0;
			foreach($videos as $video) {
				$twig['totalview'] += $video->getCounter();
			}
			foreach($videos as $key => $video) {
				$twig['videoslist'][$key]->videoformattedname = ucwords(preg_replace("/[\-_]/"," ",$video->getVideoname()));
				$twig['videoslist'][$key]->pourcent = number_format($video->getCounter() / $twig['totalview'] * 100,2)." %";
			}
            
            return $this->render('innoLCLbackBundle:List:Statistique.html.twig',$twig);
        }
        
}
