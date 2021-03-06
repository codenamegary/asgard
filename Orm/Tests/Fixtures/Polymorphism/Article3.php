<?php
namespace Asgard\Orm\Tests\Fixtures\Polymorphism;

class Article3 extends \Asgard\Entity\Entity {
	public static function definition(\Asgard\Entity\Definition $definition) {
		$definition->properties = [
			'title',
			'categories' => [
				'type'          => 'entity',
				'entity'        => 'Asgard\Orm\Tests\Fixtures\Polymorphism\Category',
				'many'          => true,
			]
		];
	}
}