<?php
namespace Asgard\Entityform;

/**
 * Create form from an entity.
 * @author Michel Hognerud <michel@hognerud.com>
 */
interface EntityFormInterface extends \Asgard\Form\FormInterface {
	/**
	 * Add another nested fields solver.
	 * @param EntityFieldSolverInterface $EntityFieldSolver
	 * @return EntityFormInterface
	 */
	public function addEntityFieldSolver($EntityFieldSolver);

	/**
	 * Return the main fields solver.
	 * @return EntityFieldSolverInterface
	 */
	public function getEntityFieldSolver();

	/**
	 * Set DataMapper dependency.
	 * @param  \Asgard\Orm\DataMapperInterface $dataMapper
	 * @return EntityFormInterface
	 */
	public function setDataMapper(\Asgard\Orm\DataMapperInterface $dataMapper);

	/**
	 * Return DataMapper dependency.
	 * @return \Asgard\Orm\DataMapperInterface
	 */
	public function getDataMapper();

	/**
	 * Return the entity.
	 * @return \Asgard\Entity\Entity
	 */
	public function getEntity();

	/**
	 * Embed an entity relation in the form.
	 * @param string $name
	 */
	public function addRelation($name);

	/**
	 * Save the entity.
	 * @return boolean true for success
	 */
	public function doSave();

	/**
	 * Return the errors of a nested-group if provided, or all.
	 * @param  \Asgard\Form\GroupInterface $group
	 * @return array
	 */
	public function errors($group=null);
}
