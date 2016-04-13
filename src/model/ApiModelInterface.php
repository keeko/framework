<?php
namespace keeko\framework\model;

interface ApiModelInterface {
	
	/**
	 * Returns the type name used for this model in the public API
	 *
	 * @return ModelSerializerInterface
	 */
	public static function getSerializer();
}