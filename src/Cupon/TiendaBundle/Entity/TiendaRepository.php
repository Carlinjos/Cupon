<?php
namespace Cupon\TiendaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TiendaRepository extends EntityRepository 
{
	public function findUltimasOfertasPublicadas($tienda_id, $limite = 10)
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT o, t
				  FROM OfertaBundle:Oferta o
				  JOIN o.tienda t
				 WHERE o.revisada = true
				   AND o.fechaPublicacion < :fecha
				   AND o.tienda = :id
			  ORDER BY o.fechaExpiracion DESC';

		$consulta = $em->createQuery($dql);
		$consulta->setParameter('id', $tienda_id);
		$consulta->setParameter('fecha', new \DateTime('now'));
		$consulta->setMaxResults($limite);

		return $consulta->getResult();
	}

	public function findCercanas($tienda, $ciudad)
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT t, c
				  FROM TiendaBundle:Tienda t
				  JOIN t.ciudad c
				 WHERE c.slug = :ciudad
				   AND t.slug != :tienda';

		$consulta = $em->createQuery($dql);
		$consulta->setParameter('ciudad', $ciudad);
		$consulta->setParameter('tienda', $tienda);
		$consulta->setMaxResults(5);

		return $consulta->getResult();
	}
}