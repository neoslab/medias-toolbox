<?php
/**
 * Register all actions and filters for the plugin
 *
 * @link https://neoslab.com
 * @since 1.0.0
 *
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/includes
*/

/**
 * Class `Medias_Toolbox_Loader`
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/includes
 * @author NeosLab <support@neoslab.com>
*/
class Medias_Toolbox_Loader
{
	/**
	 * The array of actions registered with WordPress.
	 * @since 1.0.0
	 * @access protected
	 * @var array $actions the actions registered with WordPress to fire when the plugin loads
	*/
	protected $actions;

	/**
	 * The array of filters registered with WordPress
	 * @since 1.0.0
	 * @access protected
	 * @var array $filters the filters registered with WordPress to fire when the plugin loads
	*/
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters
	 * @since 1.0.0
	*/
	public function __construct()
	{
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress
	 * @since 1.0.0
	 * @param string $hook the name of the WordPress action that is being registered
	 * @param object $component a reference to the instance of the object on which the action is defined
	 * @param string $callback the name of the function definition on the $component
	 * @param int $priority optional (The priority at which the function should be fired - Default is 10)
	 * @param int $acceptedArgs optional (The number of arguments that should be passed to the $callback - Default is 1)
	*/
	public function add_action($hook, $component, $callback, $priority = 10, $acceptedArgs = 1)
	{
		$this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $acceptedArgs);
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress
	 * @since 1.0.0
	 * @param string $hook the name of the WordPress action that is being registered
	 * @param object $component a reference to the instance of the object on which the action is defined
	 * @param string $callback the name of the function definition on the $component
	 * @param int $priority optional (The priority at which the function should be fired - Default is 10)
	 * @param int $acceptedArgs optional (The number of arguments that should be passed to the $callback - Default is 1)
	*/
	public function add_filter($hook, $component, $callback, $priority = 10, $acceptedArgs = 1)
	{
		$this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $acceptedArgs);
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single collection
	 * @since 1.0.0
	 * @access private
	 * @param array $hooks the collection of hooks that is being registered
	 * @param string $hook the name of the WordPress filter that is being registered
	 * @param object $component a reference to the instance of the object on which the filter is defined
	 * @param string $callback the name of the function definition on the $component
	 * @param int $priority the priority at which the function should be fired
	 * @param int $acceptedArgs the number of arguments that should be passed to the $callback
	 * @return array the collection of actions and filters registered with WordPress
	*/
	private function add($hooks, $hook, $component, $callback, $priority, $acceptedArgs)
	{
		$hooks[] = array('hook' => $hook, 'component' => $component, 'callback' => $callback, 'priority' => $priority, 'acceptedArgs' => $acceptedArgs);
		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress
	 * @since 1.0.0
	*/
	public function run()
	{
		foreach ($this->filters as $hook)
		{
			add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['acceptedArgs']);
		}

		foreach ($this->actions as $hook)
		{
			add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['acceptedArgs']);
		}
	}
}

?>