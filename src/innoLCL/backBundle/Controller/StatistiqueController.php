<?php
namespace innoLCL\backBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;

class StatistiqueController extends Controller
{
        public function videoAction() {
            $fs = new Filesystem();
			$twig = array('currentview' => "video");
			$em = $this->getDoctrine()->getManager();
			$repositoryVideo = $em->getRepository('innoLCL\StatBundle\Entity\VideoStat');

			$videos = $repositoryVideo->findAll();

			$twig['videoslist'] = array('divers' => array('totalview' => 0,'titre'=> 'Divers Vidéo','videos' => array()),'laureats' => array('totalview' => 0,'titre'=> 'Vidéo des idées des lauréats','videos' => array()));

			foreach($videos as $video) {
                if($fs->exists('./video/'.$video->getVideoname().'.mp4')) {
                    $twig['videoslist']['divers']['totalview'] += $video->getCounter();
                    $twig['videoslist']['divers']['videos'][] = $video;
                }
                if($fs->exists('./video/idealaureat/'.$video->getVideoname().'.mp4')) {
                    $twig['videoslist']['laureats']['totalview'] += $video->getCounter();
                    $twig['videoslist']['laureats']['videos'][] = $video;
                }
			}
			foreach($twig['videoslist']['divers']['videos'] as $key => $video) {
				$twig['videoslist']['divers']['videos'][$key]->videoformattedname = ucwords(preg_replace("/[\-_]/"," ",$video->getVideoname()));
				$twig['videoslist']['divers']['videos'][$key]->pourcent = number_format($video->getCounter() / $twig['videoslist']['divers']['totalview'] * 100,2)." %";
			}
			foreach($twig['videoslist']['laureats']['videos'] as $key => $video) {
				$twig['videoslist']['laureats']['videos'][$key]->videoformattedname = ucwords(preg_replace("/[\-_]/"," ",$video->getVideoname()));
				$twig['videoslist']['laureats']['videos'][$key]->pourcent = number_format($video->getCounter() / $twig['videoslist']['laureats']['totalview'] * 100,2)." %";
			}

            return $this->render('innoLCLbackBundle:Statistiques:Video.html.twig',$twig);
        }

        public function laureatsAction() {
            $twig = array('currentview' => "laureats");

            $em = $this->getDoctrine()->getManager();
            $repoIdeaLaureat = $em->getRepository('innoLCL\bothIdeaBundle\Entity\IdeaLaureat');
            $repoVote = $em->getRepository('innoLCL\StatBundle\Entity\Votes');

            $twig['totalvote'] = 0;
            $twig['IdeaLaureats'] = $repoIdeaLaureat->findBy(array(),
                                                            array('nbVotes' => 'DESC',
                                                            'prenomAuthor' => 'ASC'));

            foreach($twig['IdeaLaureats'] as $idealaureat) {
                $twig['totalvote'] += $idealaureat->getNbVotes();
        	}
            foreach($twig['IdeaLaureats'] as $key => $idealaureat) {
				$twig['IdeaLaureats'][$key]->pourcent = number_format($idealaureat->getNbVotes() / $twig['totalvote'] * 100,2)." %";
			}

            return $this->render('innoLCLbackBundle:Statistiques:Laureats.html.twig',$twig);
        }

}
