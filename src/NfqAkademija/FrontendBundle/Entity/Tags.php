<?php

namespace NfqAkademija\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections;
/**
 * Tags
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity
 */
class Tags
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $photos;
     *
     * @ORM\ManyToMany(targetEntity="NfqAkademija\FrontendBundle\Entity\Photo", mappedBy="tags")
     */
    protected $photos;


    public function __construct()
    {
        $this->photos = new Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

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
     * Set name
     *
     * @param string $name
     * @return Tags
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add photo
     *
     * @param \NfqAkademija\FrontendBundle\Entity\Photo $photo
     * @return Tags
     */
    public function addPhoto(\NfqAkademija\FrontendBundle\Entity\Photo $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \NfqAkademija\FrontendBundle\Entity\Photo $photo
     */
    public function removePhoto(\NfqAkademija\FrontendBundle\Entity\Photo $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }
}
