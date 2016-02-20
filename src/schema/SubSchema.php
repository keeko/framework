<?php
namespace keeko\framework\schema;

use phootwork\lang\Arrayable;

abstract class SubSchema implements Arrayable {

	/** @var PackageSchema */
	protected $package;
	
	public function __construct(RootSchema $root = null, $contents = []) {
		$this->package = $root;
		$this->parse($contents);
	}
	
	protected function parse($contents) {
	}
	
	/**
	 * @return PackageSchema
	 */
	public function getPackage() {
		return $this->package;
	}
	
	public function setPackage(RootSchema $package) {
		$this->package = $package;
	}
}