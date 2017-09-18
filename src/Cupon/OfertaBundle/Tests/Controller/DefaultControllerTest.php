<?php
namespace Cupon\OfertaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
	/** @test */
	public function laPortadaSimpleRedirigeAUnaCiudad()
	{
		$client = static::createClient();
		$client->request('GET', '/');
		$this->assertEquals(302, $client->getResponse()->getStatusCode(), 'La portada redirige a la portada de una ciudad (status 302)');
	}

	/** @test */
	public function laPortadaSoloMuestraUnaOfertaActiva()
	{
		$client = static::createClient();
		$client->followRedirects(true);
		$crawler = $client->request('GET', '/');
		$ofertasActivas = $crawler->filter('article.oferta section.descripcion a:contains("Comprar")')->count();
		$this->assertEquals(1, $ofertasActivas, 'La portada muestra una única oferta activa que se puede comprar');
	}

	/** @test */
	public function losUsuariosPuedenRegistrarseDesdeLaPortada()
	{
		$client = static::createClient();
		$client->request('GET', '/');
		$crawler = $client->followRedirect();
		$numeroEnlacesRegistrarse = $crawler->filter('html:contains("Regístrate ya")')->count();
		$this->assertGreaterThan(0, $numeroEnlacesRegistrarse,'La portada muestra al menos un enlace o botón para registrarse');
	}

	/** @test */
	public function losUsuariosAnonimosVenLaCiudadPorDefecto()
	{
		$client = static::createClient();
		$client->followRedirects(true);
		$crawler = $client->request('GET', '/');
		$ciudadPorDefecto = $client->getContainer()->getParameter('cupon.ciudad_por_defecto');
		$ciudadPortada = $crawler->filter('header nav select option[selected="selected"]')->attr('value');
		$this->assertEquals($ciudadPorDefecto, $ciudadPortada,'Los usuarios anónimos ven seleccionada la ciudad por defecto');
	}

	/** @test */
	public function losUsuariosAnonimosDebenLoguearseParaPoderComprar()
	{
		$client = static::createClient();
		$client->request('GET', '/');
		$crawler = $client->followRedirect();
		$comprar = $crawler->selectLink('Comprar')->link();
		$client->click($comprar);
		$crawler = $client->followRedirect();
		$this->assertRegExp('/.*\/usuario\/login_check/', $crawler->filter('article form')->attr('action'), 
			'Después de pulsar el botón de comprar, el usuario anónimo ve el formulario de login');
	}

	/** @test */
	public function laPortadaRequierePocasConsultasDeBaseDeDatos()
	{
		$client = static::createClient();
		$client->request('GET', '/');
		if ($profiler = $client->getProfile()) 
		{
			$this->assertLessThan(4, count($profiler->getCollector('db')->getQueries()),
				'La portada requiere menos de 4 consultas a la base de datos');
		}
	}

	/** @test */
	public function laPortadaSeGeneraMuyRapido()
	{
		$client = static::createClient();
		$client->request('GET', '/');
		if ($profiler = $client->getProfile()) 
		{
			$this->assertLessThan(500, $profiler->getCollector('time')->getTotalTime(),
			'La portada se genera en menos de medio segundo');
		}
	}

	public function generaUsuarios()
	{
		return array(
				array(
					array('frontend_usuario[nombre]' => 'Anónimo',
						  'frontend_usuario[apellidos]' => 'Apellido1 Apellido2',
						  'frontend_usuario[email]' =>
						  'anonimo'.uniqid().'@localhost.localdomain',
						  'frontend_usuario[password][first]' => 'anonimo1234',
						  'frontend_usuario[password][second]' => 'anonimo1234',
						  'frontend_usuario[direccion]' => 'Calle ...',
						  'frontend_usuario[dni]' => '11111111H',
						  'frontend_usuario[numero_tarjeta]' => '123456789012345',
						  'frontend_usuario[ciudad]' => '1',
						  'frontend_usuario[permite_email]' => '1')
					)
				);
	}

	/**
	* @dataProvider generaUsuarios
	*/
	public function testRegistro($usuario)
	{
		$client = static::createClient();
		$client->followRedirects(true);
		$crawler = $client->request('GET', '/');
		$enlaceRegistro = $crawler->selectLink('Regístrate ya')->link();
		$crawler = $client->click($enlaceRegistro);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Regístrate gratis como usuario")')->count(),
			'Después de pulsar el botón Regístrate de la portada, se carga la página con el formulario de registro');

		$formulario = $crawler->selectButton('Registrarme')->form($usuario);
		$crawler = $client->submit($formulario);
		$this->assertTrue($client->getResponse()->isSuccessful());
		$this->assertTrue($client->getCookieJar()->get('MOCKSESSID'),'La aplicación ha enviado una cookie de sesión');

		$perfil = $crawler->filter('aside section#login')->selectLink('Ver mi perfil')->link();
		$crawler = $client->click($perfil);
		$this->assertEquals($usuario['frontend_usuario[email]'],
			$crawler->filter('form input[name="frontend_usuario[email]"]')->attr('value'),
			'El usuario se ha registrado correctamente y sus datos se han guardado en la base de datos');
	}
}