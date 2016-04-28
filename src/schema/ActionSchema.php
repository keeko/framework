<?php
namespace keeko\framework\schema;

use phootwork\collection\ArrayList;
use phootwork\collection\Map;

class ActionSchema extends SubSchema {
	
	const ACL_GUEST = 'guest';
	const ACL_USER = 'user';
	const ACL_ADMIN = 'admin';

	/** @var string */
	private $name;
	
	/** @var string */
	private $title;
	
	/** @var string */
	private $description;
	
	/** @var string */
	private $class;

	/** @var ArrayList<string> */
	private $acl;
	
	/** @var ArrayList */
	private $l10n;
	
	/** @var ArrayList */
	private $scripts;
	
	/** @var ArrayList */
	private $styles;
	
	/** @var Map<string, string> */
	private $response;
	
	public function __construct($name, PackageSchema $package = null, $contents = []) {
		$this->name = $name;
		parent::__construct($package, $contents);
	}
	
	/**
	 * @param array $contents
	 */
	protected function parse($contents) {
		$data = new Map($contents);
	
		$this->title = $data->get('title', '');
		$this->class = $data->get('class', '');
		$this->description = $data->get('description', '');
		
		$this->acl = new ArrayList($data->get('acl', []));
		$this->response = new Map($data->get('response', []));
		$this->l10n = new ArrayList($data->get('l10n', []));
		$this->scripts = new ArrayList($data->get('scripts', []));
		$this->styles = new ArrayList($data->get('styles', []));
	}
	
	public function toArray() {
		$arr = [
			'title' => $this->title,
			'description' => $this->description,
			'class' => $this->class,
			'acl' => $this->acl->toArray(),
			'response' => $this->response->toArray(),
			'l10n' => $this->l10n->toArray(),
			'scripts' => $this->scripts->toArray(),
			'styles' => $this->styles->toArray()
		];

		$ret = [];
		foreach ($arr as $k => $v) {
			if (!empty($v)) {
				$ret[$k] = $v;
			}
		}
		
		return $ret;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function setClass($class) {
		$this->class = $class;
		return $this;
	}
	
	public function hasAcl($group) {
		return $this->acl->contains($group);
	}
	
	public function addAcl($group) {
		$this->acl->add($group);
		return $this;
	}
	
	public function removeAcl($group) {
		$this->acl->remove($group);
		return $this;
	}
	
	public function setAcl($groups) {
		$this->acl = new ArrayList($groups);
		return $this;
	}
	
	/**
	 * @return ArrayList
	 */
	public function getAcl() {
		return $this->acl;
	}
	
	/**
	 * Checks whether a response with the given type is present
	 * 
	 * @param string $type
	 * @return boolean
	 */
	public function hasResponse($type) {
		return $this->response->has($type);
	}
	
	public function setResponse($type, $class) {
		$this->response->set($type, $class);
	}
	
	public function getResponse($type) {
		return $this->response->get($type);
	}
	
	/**
	 * @return ArrayList
	 */
	public function getL10n() {
		return $this->l10n;
	}
	
	/**
	 * @return ArrayList
	 */
	public function getScripts() {
		return $this->scripts;
	}
	
	/**
	 * @return ArrayList
	 */
	public function getStyles() {
		return $this->styles;
	}
}
