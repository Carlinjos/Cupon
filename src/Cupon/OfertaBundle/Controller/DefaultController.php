<?php

namespace Cupon\OfertaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    public function portadaAction($ciudad)
    {
        $em = $this->getDoctrine()->getManager();
		$oferta = $em->getRepository('OfertaBundle:Oferta')->findOfertaDelDia($ciudad);

		if( !isset($oferta) )
			throw $this->createNotFoundException('No se ha encontrado la oferta del día en la ciudad seleccionada');

        return $this->render( 'OfertaBundle:Default:portada.html.twig', array('oferta' => $oferta) );
    }
}
