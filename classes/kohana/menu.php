<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Menu
{

	const STR_COMP_MODE_EXACT = 'str_comp_exact';
	const STR_COMP_MODE_CONTAINS = 'str_comp_contains';

	public static $str_comp_mode = self::STR_COMP_MODE_EXACT;

	protected $config;
	protected $menu;
	protected $view;

	/**
	 * @param	string	$config	 see factory()
	 */
	public function __construct($config)
	{
		$this->config = Kohana::config($config);
		$this->view = View::factory($this->config['view']);
		$this->menu = array('items' => &$this->config['items']);
		$this->menu = Menu::process_urls($this->menu);
	}

	/**
	 * @param	string	$config	the config file that contains the menu array
	 * @return	Menu
	 */
	public static function factory($config = 'default')
	{
		return new Menu('menu/'.$config);
	}

	public function render()
	{
		return $this->view->bind('menu', $this->menu)->render();
	}

	public function set_current($url = '')
	{
		$item =& Menu::get_item_by_url($url, $this->menu);
		if ( ! empty($item))
		{
			if ( ! isset($item['classes']))
				$item['classes'] = array();
			$item['classes'][] = $this->config['current_class'];
		}
		return $this;
	}

	public function set_title($url, $title)
	{
		$item =& Menu::get_item_by_url($url, $this->menu);
		if ( ! empty($item))
			$item['title'] = $title;
		return $this;
	}

	public function set_url($url, $new_url)
	{
		$item =& Menu::get_item_by_url($url, $this->menu);
		if ( ! empty($item))
			$item['url'] = $new_url;
		return $this;
	}

	public function add_class($url, $class)
	{
		$item =& Menu::get_item_by_url($url, $this->menu);
		if ( ! empty($item))
		{
			if ( ! isset($item['classes']))
				$item['classes'] = array();
			if ( ! in_array($class, $item['classes']))
				$item['classes'][] = $class;
		}
		return $this;
	}

	public function remove_class($url, $class)
	{
		$item =& Menu::get_item_by_url($url, $this->menu);
		if ( ! empty($item) AND isset($item['classes']))
		{
			$matching_class_key = array_search($class, $item['classes']);
			if ($matching_class_key)
				unset($item['classes'][$matching_class_key], $matching_class_key);
		}
		return $this;
	}

	/**
	 * Recursively apply URL::site to all internal links.
	 * @param	array	$menu	a menu items
	 * @return	array	the processed menu item
	 */
	protected static function process_urls(array &$menu)
	{
		if (isset($menu['url']))
		{
			if (    ! 'http://'  == substr($menu['url'], 0, 7)
				AND ! 'https://' == substr($menu['url'], 0, 8))
			{
				$menu['url'] = URL::site($menu['url']);
			}
		}
		if (isset($menu['items']))
		{
			foreach ($menu['items'] as $key => &$item)
				$menu['items'][$key] = Menu::process_urls($menu['items'][$key]);
		}
		return $menu;
	}

	/**
	 * @param	string	$url	the link url to search for
	 * @param	array	$menu	the whole menu array or a sublevel
	 * @return	array	the first matching item or an empty array
	 */
	protected static function &get_item_by_url($url, array &$menu)
	{
		$null_array = array();
		if ( ! isset($menu['items']))
			return $null_array;
		foreach ($menu['items'] as &$item)
		{
			if (Menu::strings_match($item['url'], $url))
			{
				return $item;
			}
			else
			{
				$result_from_child_items =& Menu::get_item_by_url($url, $item);
				if ( ! empty($result_from_child_items))
					return $result_from_child_items;
			}
		}
		return $null_array;
	}

	protected static function strings_match($a, $b)
	{
		return call_user_func(array('Menu', Menu::$str_comp_mode), $a, $b);
	}

	public static function str_comp_exact($a, $b)
	{
		return $a == $b;
	}

	public static function str_comp_contains($a, $b)
	{
		return (bool) strstr($a, $b);
	}

}