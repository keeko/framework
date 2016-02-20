<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;
use phootwork\collection\ArrayList;
use phootwork\lang\Arrayable;

class AutoloadSchema implements Arrayable {

	/** @var PsrSchema */
	private $psr0;
	
	/** @var PsrSchema */
	private $psr4;
	
	/** @var Map<string, string> */
	private $classmap;
	
	/** @var Map<string, string> */
	private $files;

	public function __construct($contents = []) {
		$this->parse($contents);
	}
		
	private function parse($contents) {
		$data = new Map($contents);
	
		$this->psr0 = new PsrSchema($data->get('psr-0', []));
		$this->psr4 = new PsrSchema($data->get('psr-4', []));
		$this->classmap = $data->get('classmap', new Map());
		$this->files = $data->get('files', new Map());
	}
	
	public function toArray() {
		$arr = [];
		
		if (!$this->psr4->isEmpty()) {
			$arr['psr-4'] = $this->psr4->toArray();
		}
		
		if (!$this->psr0->isEmpty()) {
			$arr['psr-0'] = $this->psr0->toArray();
		}
		
		if (!$this->classmap->isEmpty()) {
			$arr['classmap'] = $this->classmap->toArray();
		}
		
		if (!$this->files->isEmpty()) {
			$arr['files'] = $this->files->toArray();
		}

		return $arr;
	}
	
	/**
	 * @return PsrSchema
	 */
	public function getPsr0() {
		return $this->psr0;
	}

	/**
	 * @return PsrSchema
	 */
	public function getPsr4() {
		return $this->psr4;
	}

	/**
	 * @return ArrayList
	 */
	public function getClassmap() {
		return $this->classmap;
	}

	/**
	 * @return ArrayList
	 */
	public function getFiles() {
		return $this->files;
	}

}
