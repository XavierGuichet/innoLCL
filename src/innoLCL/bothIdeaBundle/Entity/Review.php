<?php

namespace innoLCL\bothIdeaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Review
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="innoLCL\bothIdeaBundle\Entity\ReviewRepository")
 */
class Review
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
     * @ORM\Column(name="Commentaire", nullable=true, type="text")
     */
    private $commentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="Avis", type="string", columnDefinition="enum('notmoderated', 'maybe', 'validated', 'refused')")
     * 
     * @Assert\Choice(choices = {null,"notmoderated", "maybe", "validated", "refused"})
     */
    private $avis;

    /**
     * @var versionIdea
     *
     * @ORM\Column(name="versionIdea", type="integer")
     * 
     */
    private $versionIdea;
    
    /**
	 * @ORM\ManyToOne(targetEntity="innoLCL\bothIdeaBundle\Entity\Idea",inversedBy="reviews")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $idea;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="innoLCL\AllUserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $moderateur;


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
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Review
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set avis
     *
     * @param string $avis
     *
     * @return Review
     */
    public function setAvis($avis)
    {
        $this->avis = $avis;

        return $this;
    }

    /**
     * Get avis
     *
     * @return string
     */
    public function getAvis()
    {
        return $this->avis;
    }

    /**
     * Set versionIdea
     *
     * @param integer $versionIdea
     *
     * @return Review
     */
    public function setVersionIdea($versionIdea)
    {
        $this->versionIdea = $versionIdea;

        return $this;
    }

    /**
     * Get versionIdea
     *
     * @return integer
     */
    public function getVersionIdea()
    {
        return $this->versionIdea;
    }

    /**
     * Set idea
     *
     * @param \innoLCL\bothIdeaBundle\Entity\Idea $idea
     *
     * @return Review
     */
    public function setIdea(\innoLCL\bothIdeaBundle\Entity\Idea $idea)
    {
        $this->idea = $idea;

        return $this;
    }

    /**
     * Get idea
     *
     * @return \innoLCL\bothIdeaBundle\Entity\Idea
     */
    public function getIdea()
    {
        return $this->idea;
    }

    /**
     * Set moderateur
     *
     * @param \innoLCL\AllUserBundle\Entity\User $moderateur
     *
     * @return Review
     */
    public function setModerateur(\innoLCL\AllUserBundle\Entity\User $moderateur)
    {
        $this->moderateur = $moderateur;

        return $this;
    }

    /**
     * Get moderateur
     *
     * @return \innoLCL\AllUserBundle\Entity\User
     */
    public function getModerateur()
    {
        return $this->moderateur;
    }
}
