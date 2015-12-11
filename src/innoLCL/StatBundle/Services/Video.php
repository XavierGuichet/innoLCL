<?php
// src/innoLCL/StatBundle/Service/Video.php

namespace innoLCL\StatBundle\Services;

use innoLCL\StatBundle\Entity\VideoStat;
use Symfony\Component\Filesystem\Filesystem;

class Video {

  /**
   * Vérifie si la video existe sur le serveur
   *
   * @param string $text
   * @return bool
   */

  public function videoExist($name)
  {
	$fs = new Filesystem();
	return $fs->exists('./video/'.$name.'.mp4');	
  }

}
