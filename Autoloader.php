<?php
namespace Coxis\Core;

class Autoloader {
	public static $map = array(
		// 'Something'	=>	'there/somewhere.php',
	);
	public static $directories = array(
		// 'App'	=>	'app',
	);
	public static $preloaded = array(
		// array('Somewhere', 'there/somewhere.php'),
	);
	
	public static function map($class, $path) {
		static::$map[$class] = $path;
	}
	
	public static function dir($k, $v) {
		static::$directories[$k] = $v;
	}

	public static function addPreloadedClasses($classes) {
		if(!\Coxis\Core\App::get('config')->get('global_namespace') || !\Coxis\Core\App::get('config')->get('preload'))
			return;
		foreach($classes as $class)
			static::$preloaded[] = $class;
		#remove duplicate files
		static::$preloaded = array_unique(static::$preloaded, SORT_REGULAR);
	}

	public static function preloadClass($class, $file) {
		if(!\Coxis\Core\App::get('config')->get('global_namespace') || !\Coxis\Core\App::get('config')->get('preload'))
			return;
		if(!array_search(realpath($file), static::$preloaded));
			static::$preloaded[] = array(strtolower($class), realpath($file));
	}
	
	public static function preloadDir($file) {
		if(!\Coxis\Core\App::get('config')->get('global_namespace') || !\Coxis\Core\App::get('config')->get('preload'))
			return array();

		static::$preloaded = array_unique(static::fetchPreloadDir($file), SORT_REGULAR);
	}

	public static function fetchPreloadDir($file) {
		return \Coxis\Utils\Cache::get('Coxis\Core\Autoloader\preloadDir\\'.$file, function() use($file) {
			$preload = array();
			if(is_dir($file) && !strpos($file, '.') !== 0) {
				foreach(glob($file.'/*') as $sub_file)
					$preload = array_merge($preload, static::fetchPreloadDir($sub_file));
			}
			else {
				if(!preg_match('/^[A-Z]{1}[a-zA-Z0-9_]+.php$/', basename($file)))
					return array();
				list($class) = explode('.', basename($file));
				if(!array_search(realpath($file), static::$preloaded));
					$preload[] = array(strtolower($class), realpath($file));
			}
			return $preload;
		});
	}
	
	public static function loadClass($class) {
		if(class_exists($class))
			return;
		
		$dir = \Coxis\Utils\NamespaceUtils::dirname($class);

		if(\Coxis\Core\App::hasInstance())
			$importer = App::get('importer');
		else
			$importer = new Importer;
		$importer->_import($class, array('into'=>$dir));
	}
}