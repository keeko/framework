<?php
namespace keeko\framework\foundation;

use keeko\core\model\Package;

interface PackageInterface {
	
	/**
	 * Returns the name
	 *
	 * @return string
	 */
	public function getName();
	
	/**
	 * Returns the canonical name
	 *
	 * @return string
	 */
	public function getCanonicalName();
	
	/**
	 * Returns the title
	 *
	 * @return string
	 */
	public function getTitle();
	
	/**
	 * Returns the associated model
	 *
	 * @return Package
	 */
	public function getModel();
}