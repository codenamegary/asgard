#Config

[![Build Status](https://travis-ci.org/asgardphp/config.svg?branch=master)](https://travis-ci.org/asgardphp/config)

The Config package helps you manage the configuration of your application.

- [Installation](#installation)
- [Usage in the Asgard Framework](#usage-asgard)
- [Usage outside the Asgard Framework](#usage-outside)
- [methods](#methods)
- [structure](#structure)
- [Commands](#commands)

<a name="installation"></a>
##Installation
**If you are working on an Asgard project you don't need to install this library as it is already part of the standard libraries.**

	composer require asgard/config 0.*

<a name="usage-asgard"></a>
##Usage in the Asgard Framework

	$config = $container['config'];

container is usually available as a parameter, an object attribute or through Asgard\Container\Container::instance();

<a name="usage-outside"></a>
##Usage outside the Asgard Framework

	$config = new \Asgard\Config\Config;

<a name="methods"></a>
##Methods

Config inherits [\Asgard\Common\Bag](http://asgardphp.com/docs/bag) to access its data.

Besides, you can load a configuration with:

	$config->loadFile('file.yml');

Or a whole directory:

	$config->loadDir('config/');

<a name="structure"></a>
##Configuration file structure

A configuration file, like config.yml contains an array of parameters in YAML format:

	database:
		host: localhost
		user: root
		password:
		database: asgard

Each key can be acessed like:

	$config->get('database.user');
	#or
	$config['database.user'];
	#or
	$config['database']['user'];

<a name="commands"></a>
##Commands

###Contributing

Please submit all issues and pull requests to the [asgardphp/asgard](http://github.com/asgardphp/asgard) repository.

### License

The Asgard framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)