<?php

class MenuTest extends PHPUnit_Framework_TestCase
{

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
		$menu = Menu::factory('example');
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
		$menu = Menu::factory('example');
		$className = 'unittest';
		$menu->add_class('documentation', $className);
		$exampleConfig = $this->getProtectedArray($menu, 'config');
		$this->assertEquals($exampleConfig['items'][2]['classes'][0], $className);
		$this->assertEquals(count($exampleConfig['items'][2]['classes']), 1);
	}

}