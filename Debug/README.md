#Debug

[![Build Status](https://travis-ci.org/asgardphp/debug.svg?branch=master)](https://travis-ci.org/asgardphp/debug)

The debug package helps to handle errors and display debugging information to the developer.

- [Installation](#installation)
- [ErrorHandler](#errorhandler)
- [Debug](#debug)

<a name="installation"></a>
##Installation
**If you are working on an Asgard project you don't need to install this library as it is already part of the standard libraries.**

	composer require asgard/debug 0.*

<a name="errorhandler"></a>
##ErrorHandler

Register an error handler:

	$errorHandler = \Asgard\Debug\ErrorHandler::register();

Ignore PHP errors in a specific directory:

	$errorHandler->ignoreDir('libs/old_legay_package/');

Set the logger:

	$errorHandler->setLogger($logger);

The logger should implement [\Psr\Log\LoggerInterface](https://github.com/php-fig/log/blob/master/Psr/Log/LoggerInterface.php).

Check if the error handler has a logger:

	$errorHandler->isLogging();

Get the backtrace from an exception:

	$trace = $errorHandler->getBacktraceFromException($e);

To log PHP errors:

	$errorHandler->setLogPHPErrors(true);

Log an exception:

	$errorHandler->logException($e);

Log an error:

	$errorHandler->log($severity, $message, $file, $line, $trace);

Severity should be [one of the these](https://github.com/php-fig/log/blob/master/Psr/Log/LogLevel.php).

<a name="debug"></a>
##Debug

Display the debug screen:

	\Asgard\Debug\d($var1, $var2, ...);

In an Asgard application, the global function d() usually does the same.