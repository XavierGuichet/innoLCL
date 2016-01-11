<?php

namespace innoLCL\StatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Votes
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="innoLCL\StatBundle\Entity\VotesRepository")
 */
class Votes
{
    public function __construct()
    {
      $this->dateVote = new \DateTime();
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="innoLCL\bothIdeaBundle\Entity\IdeaLaureat")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $ideaLaureat;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="innoLCL\AllUserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $user;

    /**
     * @var \Date
     *
     * @ORM\Column(name="Date_Vote", type="date")
     */
    private $dateVote;


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
     * Set ideaLaureat
     *
     * @param integer $ideaLaureat
     *
     * @return Votes
     */
    public function setIdeaLaureat($ideaLaureat)
    {
        $this->ideaLaureat = $ideaLaureat;

        return $this;
    }

    /**
     * Get ideaLaureat
     *
     * @return integer
     */
    public function getIdeaLaureat()
    {
        return $this->ideaLaureat;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Votes
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set dateVote
     *
     * @param \DateTime $dateVote
     *
     * @return Votes
     */
    public function setDateVote($dateVote)
    {
        $this->dateVote = $dateVote;

        return $this;
    }

    /**
     * Get dateVote
     *
     * @return \DateTime
     */
    public function getDateVote()
    {
        return $this->dateVote;
    }
}
