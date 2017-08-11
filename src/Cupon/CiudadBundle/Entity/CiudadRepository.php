<?php
namespace Cupon\CiudadBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CiudadRepository extends EntityRepository
{
	public function findCercanas($ciudad_id)
	{
		$em = $this->getEntityManager();

		$dql = 'SELECT c
				  FROM CiudadBundle:Ciudad c
				 WHERE c.id != :id
			  ORDER BY c.nombre ASC';	  
		$consulta = $em->createQuery($dql);
		$consulta->setParameter('id', $ciudad_id);
		$consulta->setMaxResults(5);

		return $consulta->getResult();
	}
}