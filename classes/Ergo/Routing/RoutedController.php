<?php

/**
 * Routes a request to a controller method based on path and request method.
 */
class Ergo_Routing_RoutedController
	implements Ergo_Routing_Controller
{
	private $_routeMap;
	private $_controllerFactory;
	private $_controllers=array();
	private $_filterChain;

	/**
	 * @param Ergo_Routing_RouteMap
	 */
	public function __construct($routeMap=null)
	{
		$this->_filterChain = new Ergo_Routing_RequestFilterChain();
		$this->_routeMap = isset($params['routeMap']) ?
			$params['routeMap'] : new Ergo_Routing_RouteMap();
	}

	/**
	 * Adds an {@link Ergo_Routing_RequestFilter} to the controller
	 */
	public function addRequestFilter(Ergo_Routing_RequestFilter $filter)
	{
		$this->_filterChain->addFilter($filter);
		return $this;
	}

	/**
	 * Executes a php script in the context of the controller for configuration
	 */
	public function configure($script)
	{
		require($script);
	}

	/**
	 * Sets an optional controller factory to use to build controllers
	 */
	public function setControllerFactory(Ergo_Routing_ControllerFactory $factory)
	{
		$this->_controllerFactory = $factory;
	}

	/**
	 * Gets a controller for a path, either from a locally registered
	 * controller or one from a controller factory
	 */
	private function _controllerFor($name)
	{
		if(isset($this->_controllers[$name]) && is_string($this->_controllers[$name]))
		{
			return $this->_controllerFor($this->_controllers[$name]);
		}
		else if(isset($this->_controllers[$name]) && is_object($this->_controllers[$name]))
		{
			return $this->_controllers[$name];
		}
		else if(isset($this->_controllerFactory))
		{
			return $this->_controllerFactory->createController($name);
		}
		else
		{
			throw new Ergo_Routing_Exception("No controller found for $name");
		}
	}

	/* (non-phpdoc)
	 * @see Ergo_Routing_Controller::execute
	 */
	public function execute($request)
	{
		$url = $request->getUrl();
		$path = $url->getPath();

		$match = $this->_routeMap->lookup($path);
		return $this->_controllerFor($match->getName())->execute(
			new Ergo_Routing_RoutedRequest(
				$this->_filterChain->filter($request),
					$match, $this->_routeMap));
	}

	/**
	 * Defines a url, a route name and an optional controller
	 * @param $url string the url to connect the route to
	 * @param $name string an arbitrary controller name, must be unique
	 * @param $controller mixed either a controller class, or the name of another route
	 */
	public function connect($url, $name, $controller=null)
	{
		$this->_routeMap->map($url, $name, $controller);

		// register the controller if one is provided
		if(!is_null($controller))
		{
			$this->_controllers[$name] = $controller;
		}

		return $this;
	}
}
