<?php
namespace keeko\framework\model;

use Tobscure\JsonApi\SerializerInterface;

interface ModelSerializerInterface extends SerializerInterface {
	
	public function hydrate($data, $model);
	
	/**
	 * Returns an array of short names to API type name.
	 *
	 * Example:
	 * return ['users' => 'user/users'];
	 */
	public function getRelationships();
	
	/**
	 * Returns all available fields
	 *
	 * @return array
	 */
	public function getFields();
	
	/**
	 * Returns the sort fields
	 *
	 * @return array
	 */
	public function getSortFields();
}