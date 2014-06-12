<?php
namespace Asgard\Core;

class BundleLoader {
	protected $path;
	protected $app;

	public function __construct() {
		$reflector = new \ReflectionClass(get_called_class());
		$this->path = dirname($reflector->getFileName());
	}

	public function setPath($path) {
		$this->path = realpath($path);
	}

	public function getPath() {
		return $this->path;
	}

	public function buildApp($app) {
	}

	public function run($app) {
		$bundleData = $app['cache']->fetch('bundles/'.$this->getID());
		if($bundleData !== false) {
			$hooks = $bundleData['hooks'];
			$routes = $bundleData['routes'];
		}
		else {
			$hooks = $app->has('hooks') ? $this->loadHooks():[];
			$routes = $app->has('resolver') ? $this->loadControllers():[];
			
			$app['cache']->save('bundles/'.$this->getID(), [
				'hooks' => $hooks,
				'routes' => $routes,
			]);
		}

		if($app->has('translator')) {
			foreach(glob($this->getPath().'/locales/'.$app['translator']->getLocale().'/*') as $file)
				$app['translator']->addResource('yaml', $file, $app['translator']->getLocale());
		}

		if($app->has('hooks'))
			$app['hooks']->hooks($hooks);

		if($app->has('resolver'))
			$app['resolver']->addRoutes($routes);

		if($app->has('console'))
			$this->loadConsole($app);
	}

	protected function loadHooks() {
		$hooks = [];
		if(file_exists($this->getPath().'/Hooks/')) {
			foreach(glob($this->getPath().'/Hooks/*.php') as $filename) {
				$class = \Asgard\Common\Tools::loadClassFile($filename);
				if(is_subclass_of($class, 'Asgard\Hook\HooksContainer'))
					$hooks = array_merge_recursive($hooks, $class::fetchHooks());
			}
		}
		return $hooks;
	}

	protected function loadConsole($app) {
		if(file_exists($this->getPath().'/Console/')) {
			foreach(glob($this->getPath().'/Console/*.php') as $filename) {
				$class = \Asgard\Common\Tools::loadClassFile($filename);
				if(is_subclass_of($class, 'Symfony\Component\Console\Command\Command'))
					$app['console']->add(new $class);
			}
		}
	}

	protected function loadControllers() {
		$routes = [];
		if(file_exists($this->getPath().'/Controllers/')) {
			foreach(glob($this->getPath().'/Controllers/*.php') as $filename) {
				$class = \Asgard\Common\Tools::loadClassFile($filename);
				if(is_subclass_of($class, 'Asgard\Http\Controller'))
					$routes = array_merge($routes, $class::fetchRoutes());
			}
		}
		return $routes;
	}

	protected function getID() {
		return sha1($this->getPath());
	}
}