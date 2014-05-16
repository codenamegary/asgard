<?php
namespace Asgard\Http\Exceptions;

class NotFoundException extends \Asgard\Http\ControllerException {
	public function __construct($msg='') {
		$response = new \Asgard\Core\Response(404);
		parent::__construct($msg, $response);
	}
}