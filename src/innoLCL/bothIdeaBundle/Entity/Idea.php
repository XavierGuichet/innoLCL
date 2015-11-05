<?php

namespace innoLCL\bothIdeaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Idea
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="innoLCL\bothIdeaBundle\Entity\IdeaRepository")
 */
class Idea
{
    public function __construct()
    {
    $this->author = 0; 
    $this->postedon = new \Datetime();
    $this->validatedon = null;
    $this->title = "";
    $this->description = "";
    $this->customerprofit = "";
    $this->partnerprofit = "";
    $this->bonuscontent = "";
    $this->commentary = "";
    $this->statuts = "notmoderated";
    $this->validated = false;
    $this->reworked = false;
    $this->selected = false;
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
     * @ORM\ManyToOne(targetEntity="innoLCL\AllUserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="postedon", type="datetime")
     * 
     * @Assert\DateTime()
     */
    private $postedon;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validatedon", type="date", nullable=true)
     * 
     * @Assert\Date()
     */
    private $validatedon;

    /**
     * @var \string
     *
     * @ORM\Column(name="refusalreason", type="text", nullable=true, length=200)
     * 
     * @Assert\Length(max = 200, maxMessage = "Votre commentaire de refus doit faire moins de {{ limit }} caractères")
     */
    private $refusalreason;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=125)
     * 
     * @Assert\NotBlank(message = "Pensez à titrer votre idée.")
     * @Assert\Length(max = 125, maxMessage = "Votre description doit tenir en moins de {{ limit }} caractères")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=200)
     * 
     * @Assert\NotBlank(message = "Pensez à décrire votre idée.")
     * @Assert\Length(max = 200, maxMessage = "Votre description doit faire moins de {{ limit }} caractères")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="customerprofit", type="string", length=200)
     * 
     * @Assert\Length(max = 200, maxMessage = "Votre description doit faire moins de {{ limit }} caractères")
     */
    private $customerprofit;

    /**
     * @var string
     *
     * @ORM\Column(name="partnerprofit", type="string", length=200)
     * 
     * @Assert\Length(max = 200, maxMessage = "Votre description doit faire moins de {{ limit }} caractères")
     */
    private $partnerprofit;

    /**
     * @var string
     *
     * @ORM\Column(name="bonuscontent", type="string", length=200)
     * 
     * @Assert\Length(max = 200)
     */
    private $bonuscontent;

    /**
     * @var string
     *
     * @ORM\Column(name="commentary", type="string", length=255)
     * 
     * @Assert\Length(max = 255, maxMessage = "Votre commentaire doit faire moins de {{ limit }} caractères")
     */
    private $commentary;

    /**
     * @var string
     *
     * @ORM\Column(name="statuts", type="string", columnDefinition="enum('notmoderated', 'maybe', 'validated', 'refused')")
     * 
     * @Assert\Choice(choices = {null,"notmoderated", "maybe", "validated", "refused"})
     */
    private $statuts;

    /**
     * @var boolean
     *
     * @ORM\Column(name="validated", type="boolean")
     */
    private $validated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reworked", type="boolean")
     */
    private $reworked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="selected", type="boolean")
     */
    private $selected;


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
     * Set author
     *
     * @param integer $author
     *
     * @return Idea
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return integer
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set postedon
     *
     * @param \DateTime $postedon
     *
     * @return Idea
     */
    public function setPostedon($postedon)
    {
        $this->postedon = $postedon;

        return $this;
    }

    /**
     * Get postedon
     *
     * @return \DateTime
     */
    public function getPostedon()
    {
        return $this->postedon;
    }

    /**
     * Set validatedon
     *
     * @param \DateTime $validatedon
     *
     * @return Idea
     */
    public function setValidatedon($validatedon)
    {
        $this->validatedon = $validatedon;

        return $this;
    }

    /**
     * Get validatedon
     *
     * @return \DateTime
     */
    public function getValidatedon()
    {
        return $this->validatedon;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Idea
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Idea
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set customerprofit
     *
     * @param string $customerprofit
     *
     * @return Idea
     */
    public function setCustomerprofit($customerprofit)
    {
        $this->customerprofit = $customerprofit;

        return $this;
    }

    /**
     * Get customerprofit
     *
     * @return string
     */
    public function getCustomerprofit()
    {
        return $this->customerprofit;
    }

    /**
     * Set partnerprofit
     *
     * @param string $partnerprofit
     *
     * @return Idea
     */
    public function setPartnerprofit($partnerprofit)
    {
        $this->partnerprofit = $partnerprofit;

        return $this;
    }

    /**
     * Get partnerprofit
     *
     * @return string
     */
    public function getPartnerprofit()
    {
        return $this->partnerprofit;
    }

    /**
     * Set bonuscontent
     *
     * @param string $bonuscontent
     *
     * @return Idea
     */
    public function setBonuscontent($bonuscontent)
    {
        $this->bonuscontent = $bonuscontent;

        return $this;
    }

    /**
     * Get bonuscontent
     *
     * @return string
     */
    public function getBonuscontent()
    {
        return $this->bonuscontent;
    }

    /**
     * Set commentary
     *
     * @param string $commentary
     *
     * @return Idea
     */
    public function setCommentary($commentary)
    {
        $this->commentary = $commentary;

        return $this;
    }

    /**
     * Get commentary
     *
     * @return string
     */
    public function getCommentary()
    {
        return $this->commentary;
    }

    /**
     * Set statuts
     *
     * @param string $statuts
     *
     * @return Idea
     */
    public function setStatuts($statuts)
    {
        $this->statuts = $statuts;

        return $this;
    }

    /**
     * Get statuts
     *
     * @return string
     */
    public function getStatuts()
    {
        return $this->statuts;
    }

    /**
     * Set validated
     *
     * @param boolean $validated
     *
     * @return Idea
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated
     *
     * @return boolean
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set reworked
     *
     * @param boolean $reworked
     *
     * @return Idea
     */
    public function setReworked($reworked)
    {
        $this->reworked = $reworked;

        return $this;
    }

    /**
     * Get reworked
     *
     * @return boolean
     */
    public function getReworked()
    {
        return $this->reworked;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     *
     * @return Idea
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * Get selected
     * 
     * @param none
     *
     * @return boolean
     */
    public function getSelected()
    {
        return $this->selected;
    }
    
    /**
     * removeHTML
     * 
     * @param none
     *
     * @return boolean
     */
    public function removeHTML() {
        $this->setTitle(strip_tags($this->title));
        $this->setDescription(strip_tags($this->description));
        $this->setCustomerprofit(strip_tags($this->customerprofit));
        $this->setPartnerprofit(strip_tags($this->partnerprofit));
        $this->setCommentary(strip_tags($this->commentary));
        $this->setBonuscontent(strip_tags($this->bonuscontent));
        if($this->refusalreason !== null) {
            $this->setBonuscontent(strip_tags($this->bonuscontent));
        }        
        return true;        
    }
    
     /**
     * sanitize
     * 
     * @param none
     *
     * @return boolean
     */
    public function sanitize() {
        if($this->removeHTML()) {
            return true;
        }
        else {
            return false;
        }
        
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Idea
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
     * Set refusalreason
     *
     * @param string $refusalreason
     *
     * @return Idea
     */
    public function setRefusalreason($refusalreason)
    {
        $this->refusalreason = $refusalreason;

        return $this;
    }

    /**
     * Get refusalreason
     *
     * @return string
     */
    public function getRefusalreason()
    {
        return $this->refusalreason;
    }
}
