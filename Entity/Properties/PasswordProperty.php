<?php
namespace Asgard\Entity\Properties;

/**
 * Password Property.
 * @author Michel Hognerud <michel@hognerud.com>
 */
class PasswordProperty extends \Asgard\Entity\Property {
	/**
	 * {@inheritDoc}
	 */
	public function getSQLType() {
		return 'varchar(255)';
	}

	/**
	 * {@inheritDoc}
	 */
	public function doSet($val, \Asgard\Entity\Entity $entity, $name) {
		try {
			$key = $entity->getDefinition()->getEntitiesManager()->getContainer()['config']['key'];
		} catch(\Exception $e) {
			$key = '';
		}
		return sha1($key.$val);
	}
}