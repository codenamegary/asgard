<?php
namespace Asgard\Orm\Tests\Entities;

class Category extends \Asgard\Core\Entity {
	public static $properties = array(
		'title',
		'description',
	);

	public static $behaviors = array(
		'Asgard\Orm\ORMBehavior'
	);

	public static $relations = array(
		'news' => array(
			'entity' => 'Asgard\Orm\Tests\Entities\News',
			'has' => 'many',
			'validation' => array(
				'relationrequired',
				'morethan' => 3
			)
		),
	);
}