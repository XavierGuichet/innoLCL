<?php

namespace innoLCL\StatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VideoStat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="innoLCL\StatBundle\Entity\VideoStatRepository")
 */
class VideoStat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="videoname", type="string", length=255)
     */
    private $videoname;

    /**
     * @var integer
     *
     * @ORM\Column(name="counter", type="integer")
     */
    private $counter;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set videoname
     *
     * @param string $videoname
     *
     * @return VideoStat
     */
    public function setVideoname($videoname)
    {
        $this->videoname = $videoname;

        return $this;
    }

    /**
     * Get videoname
     *
     * @return string
     */
    public function getVideoname()
    {
        return $this->videoname;
    }

    /**
     * Set counter
     *
     * @param integer $counter
     *
     * @return VideoStat
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer
     */
    public function getCounter()
    {
        return $this->counter;
    }
    
    public function incrementCounter() {
		$this->counter++;
        return $this;
	}
}

