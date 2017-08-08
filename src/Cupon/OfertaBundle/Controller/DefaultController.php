<?php

namespace Cupon\OfertaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function portadaAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
		$oferta = $em->getRepository('OfertaBundle:Oferta')->findOneBy(array(
			'ciudad' 			=> 1,
			//'fechaPublicacion'  => new \DateTime('today')
		));
        return $this->render( 'OfertaBundle:Default:portada.html.twig', array('oferta' => $oferta) );
    }
}
