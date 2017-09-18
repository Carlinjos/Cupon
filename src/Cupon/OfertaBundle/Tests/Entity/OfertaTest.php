<?php
namespace Cupon\OfertaBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Cupon\OfertaBundle\Entity\Oferta;

class OfertaTest extends \PHPUnit_Framework_TestCase
{
	private $validator;

	protected function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
	}

	public function testValidarSlug()
	{
		$oferta = new Oferta();
		$oferta->setNombre('Oferta de prueba');
		$slug = $oferta->getSlug();
		$this->assertEquals('oferta-de-prueba', $slug, 'El slug se asigna automáticamente a partir del nombre');
	}

	public function testValidarDescripcion()
	{
		$oferta = new Oferta();
		$oferta->setNombre('Oferta de prueba');
		$listaErrores = $this->validator->validate($oferta);
		$this->assertGreaterThan(0, $listaErrores->count(), 'La descripción no puede dejarse en blanco');
		$error = $listaErrores[0];
		$this->assertEquals('This value should not be blank.', $error->getMessage());
		$this->assertEquals('descripcion', $error->getPropertyPath());
		$oferta->setDescripcion('Descripción de prueba');
		$listaErrores = $this->validator->validate($oferta);
		$this->assertGreaterThan(0, $listaErrores->count(), 'La descripción debe tener al menos 30 caracteres');
		$error = $listaErrores[0];
		$this->assertRegExp("/This value is too short/", $error->getMessage());
		$this->assertEquals('descripcion', $error->getPropertyPath());
	}

	public function testValidarFechas()
	{
		$oferta = new Oferta();
		$oferta->setNombre('Oferta de prueba');
		$oferta->setDescripcion('Descripción de prueba - Descripción de prueba - Descripción de prueba');
		$oferta->setFechaPublicacion(new \DateTime('today'));
		$oferta->setFechaExpiracion(new \DateTime('yesterday'));
		$listaErrores = $this->validator->validate($oferta);
		$this->assertGreaterThan(0, $listaErrores->count(), 'La fecha de expiración debe ser posterior a la fecha de
publicación');
		$error = $listaErrores[0];
		$this->assertEquals('La fecha de expiración debe ser posterior a la	fecha de publicación', $error->getMessage());
		$this->assertEquals('fechaValida', $error->getPropertyPath());
	}

	public function testValidarPrecio()
	{
		$oferta = new Oferta();
		$oferta->setNombre('Oferta de prueba');
		$oferta->setDescripcion('Descripción de prueba - Descripción de prueba - Descripción de prueba');
		$oferta->setFechaPublicacion(new \DateTime('today'));
		$oferta->setFechaExpiracion(new \DateTime('tomorrow'));
		$oferta->setUmbral(3);
		$oferta->setPrecio(-10);
		$listaErrores = $this->validator->validate($oferta);
		$this->assertGreaterThan(0, $listaErrores->count(), 'El precio no puede ser un número negativo');
		$error = $listaErrores[0];
		$this->assertRegExp("/This value should be .* or more/", $error->getMessageTemplate());
		$this->assertEquals('precio', $error->getPropertyPath());
	}
}