<?php
namespace Asgard\Orm;

/**
 * Handle database storage of entities.
 * @author Michel Hognerud <michel@hognerud.com>
 */
interface DataMapperInterface {
	/**
	 * Load an entity from database.
	 * @param  string                $entityClass entity class
	 * @param  integer               $id          entity id
	 * @return \Asgard\Entity\Entity
	 */
	public function load($entityClass, $id);

	/**
	 * Create an ORM instance.
	 * @param  string          $entityClass
	 * @return ORMInterface
	 */
	public function orm($entityClass);

	/**
	 * Return all entities of a class.
	 * @param  string $entityClass
	 * @return array
	 */
	public function all($entityClass);

	/**
	 * Destroy all entities of a clas.
	 * @param  string     $entityClass
	 * @return integer
	 */
	public function destroyAll($entityClass);

	/**
	 * Destroy a specific entity.
	 * @param  string  $entityClass entity class
	 * @param  integer $id          entity id
	 * @return boolean true if success, false otherwise
	 */
	public function destroyOne($entityClass, $id);

	/**
	 * Destroy an entity.
	 * @param  \Asgard\Entity\Entity $entity
	 * @return boolean true for success, otherwise false
	 */
	public function destroy(\Asgard\Entity\Entity $entity);

	/**
	 * Return the entities manager instance.
	 * @return \Asgard\Entity\EntityManagerInterface
	 */
	public function getEntityManager();

	/**
	 * Create and store an entity.
	 * @param  string  $entityClass
	 * @param  array   $values        default entity attributes
	 * @param  boolean $force         skip validation
	 * @return \Asgard\Entity\Entity
	 */
	public function create($entityClass, $values=null, $force=false);

	/**
	 * Get DataMapper validator for entities.
	 * @param  \Asgard\Entity\Entity $entity
	 * @return \Asgard\Validation\ValidatorInterface
	 */
	public function getValidator(\Asgard\Entity\Entity $entity);

	/**
	 * Prepare the validator.
	 * @param  \Asgard\Entity\Entity        $entity
	 * @param  \Asgard\Validation\ValidatorInterface $validator
	 */
	public function prepareValidator($entity, $validator);

	/**
	 * Validate an entity.
	 * @param  \Asgard\Entity\Entity $entity
	 * @return true for valid, otherwise false
	 */
	public function valid(\Asgard\Entity\Entity $entity);

	/**
	 * Return entity errors.
	 * @param  \Asgard\Entity\Entity $entity
	 * @return array
	 */
	public function errors(\Asgard\Entity\Entity $entity);

	/**
	 * Store an entity.
	 * @param  \Asgard\Entity\Entity $entity
	 * @param  array                 $values entity attributes
	 * @param  boolean               $force  skip validation
	 * @return \Asgard\Entity\Entity $entity
	 */
	public function save(\Asgard\Entity\Entity $entity, $values=null, $force=false);

	/**
	 * Return the related entities of an entity.
	 * @param  \Asgard\Entity\Entity $entity
	 * @param  string                $name   relation name
	 * @return \Asgrd\Entity\Entity|CollectionORMInterface
	 */
	public function related(\Asgard\Entity\Entity $entity, $name);

	/**
	 * Return an CollectionOrmFactory.
	 * @return CollectionORMFactoryInterface
	 */
	public function getCollectionOrmFactory();

	/**
	 * Return an nOrmFactory.
	 * @return ORMFactoryInterface
	 */
	public function getOrmFactory();

	/**
	 * Get related entities.
	 * @param  \Asgard\Entity\Entity $entity
	 * @param  string                $name
	 * @return \Asgard\Entity\Entity|array
	 */
	public function getRelated(\Asgard\Entity\Entity $entity, $name);

	/**
	 * Get the translations table of an entity class.
	 * @param  \Asgard\Entity\Definition $definition
	 * @return string
	 */
	public function getTranslationTable(\Asgard\Entity\Definition $definition);

	/**
	 * Get the table of an entity class.
	 * @param  \Asgard\Entity\Definition $definition
	 * @return string
	 */
	public function getTable(\Asgard\Entity\Definition $definition);

	/**
	 * Return an entity with translations.
	 * @param  \Asgard\Entity\Entity $entity
	 * @param  string                $locale
	 * @return \Asgard\Entity\Entity
	 */
	public function getTranslations(\Asgard\Entity\Entity $entity, $locale=null);

	/**
	 * Return the entity relations objects.
	 * @param  \Asgard\Entity\Definition $definition
	 * @return array
	 */
	public function relations(\Asgard\Entity\Definition $definition);

	/**
	 * Get a relation object.
	 * @param  \Asgard\Entity\Definition $definition
	 * @param  string                          $name       relation name
	 * @return EntityRelation
	 */
	public function relation(\Asgard\Entity\Definition $definition, $name);

	/**
	 * Check if the definition has the relaton/
	 * @param  \Asgard\Entity\Definition $definition
	 * @param  string                          $name
	 * @return boolean
	 */
	public function hasRelation(\Asgard\Entity\Definition $definition, $name);

	/**
	 * Return the database instance.
	 * @return \Asgard\Db\DBInterface
	 */
	public function getDB();
}