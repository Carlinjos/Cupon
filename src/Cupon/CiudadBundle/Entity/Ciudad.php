<?php
namespace Cupon\CiudadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cupon\OfertaBundle\Util\Util;

/**
* @ORM\Entity
* @ORM\Table(name="ciudad")
* @ORM\Entity(repositoryClass="Cupon\CiudadBundle\Entity\CiudadRepository")
*/
class Ciudad 
{
	/** 
	* @ORM\Id
	* @ORM\Column(type="integer")
	* @ORM\GeneratedValue 
	*/
	protected $id;

	/** @ORM\Column(type="string", length=100) */
	protected $nombre;

	/** @ORM\Column(type="string", length=100) */
	protected $slug;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Ciudad
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        $this->slug = Util::getSlug($nombre);

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Ciudad
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function __toString()
	{
		return $this->getNombre();
	}
}
