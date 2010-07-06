<?php

class MenuTest extends PHPUnit_Framework_TestCase
{

	protected $config = 'example';

	/**
	 * @test
	 * @expectedException ErrorException
	 */
	public function factoryDeniesNonExistantView()
	{
		Menu::factory('nonexistant');
	}

	/**
	 * @test
	 */
	public function configIsRead()
	{
		$menu = Menu::factory($this->config);
		$exampleConfig = $this->getProtectedArray($menu, 'config');
		$this->assertEquals($exampleConfig['view'], 'menu');
		$this->assertEquals($exampleConfig['current_class'], 'current');
		$this->assertEquals($exampleConfig['items'][0]['url'], 'http://kohanaframework.org/');
	}

	protected function getProtectedArray(Menu $object, $property)
	{
		return unserialize(PHPUnit_Framework_Assert::readAttribute($object, $property));
	}

	/**
	 * @test
	 */
	public function addClass()
	{
		$menu = Menu::factory($this->config);
		$className = 'unittest';
		$menu->add_class('documentation', $className);
		$exampleConfig = $this->getProtectedArray($menu, 'config');
		$this->assertEquals($exampleConfig['items'][2]['classes'][0], $className);
		$this->assertEquals(count($exampleConfig['items'][2]['classes']), 1);
	}

	/**
	 * @test
	 */
	public function stringCast()
	{
		$menu = Menu::factory($this->config);
		$this->assertEquals($menu->render(), (string) $menu);
	}

	/**
	 * @test
	 */
	public function markCurrentItem()
	{
		$menu = Menu::factory($this->config)
			->set_current('documentation/lorem-ipsum');
		$exampleConfig = $this->getProtectedArray($menu, 'config');
		$this->assertEquals($exampleConfig['items'][2]['items'][0]['classes'][0], $exampleConfig['current_class']);
	}

	/**
	 * @test
	 */
	public function addRemoveClass()
	{
		$menu = Menu::factory($this->config)
			->add_class('documentation', 'unittest');
		$exampleConfig = $this->getProtectedArray($menu, 'config');
		$this->assertEquals($exampleConfig['items'][2]['classes'][0], 'unittest');
		$menu->remove_class('documentation', 'unittest');
		$exampleConfig = $this->getProtectedArray($menu, 'config');
		$this->assertTrue(empty($exampleConfig['items'][2]['classes']));
	}

	/**
	 * @test
	 */
	public function setTitle()
	{
		$menu = Menu::factory($this->config)
			->set_title('documentation/lorem-ipsum', 'unittest');
		$exampleConfig = $this->getProtectedArray($menu, 'config');
		$this->assertEquals($exampleConfig['items'][2]['items'][0]['title'], 'unittest');
	}

	/**
	 * @test
	 */
	public function setUrl()
	{
		$menu = Menu::factory($this->config)
			->set_url('documentation/lorem-ipsum', 'unittest');
		$exampleConfig = $this->getProtectedArray($menu, 'config');
		$this->assertEquals($exampleConfig['items'][2]['items'][0]['url'], 'unittest');
	}

	/**
	 * @test
	 */
	public function urlProcessing()
	{
		$process_urls = new ReflectionMethod('Menu', 'process_urls');
		$process_urls->setAccessible(TRUE);
		$mockMenu = array(
			'items' => array(
				array('url' => ''),
				array('url' => 'http://kohanaframework.org/'),
				array('url' => 'https://kohanaframework.org/'),
			),
		);
		$processedMenu = $process_urls->invoke(Menu::factory(), $mockMenu);
		$this->assertEquals('/', $processedMenu['items'][0]['url']);
		$this->assertEquals($mockMenu['items'][1]['url'], $processedMenu['items'][1]['url']);
		$this->assertEquals($mockMenu['items'][2]['url'], $processedMenu['items'][2]['url']);
	}

}