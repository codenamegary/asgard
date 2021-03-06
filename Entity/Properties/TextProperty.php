<?php
namespace Asgard\Entity\Properties;

/**
 * Text Property.
 * @author Michel Hognerud <michel@hognerud.com>
 */
class TextProperty extends \Asgard\Entity\Property {
	/**
	 * {@inheritDoc}
	 */
	public function getSQLType() {
		if($this->get('length'))
			return 'varchar('.$this->get('length').')';
		else
			return 'varchar(255)';
	}
}