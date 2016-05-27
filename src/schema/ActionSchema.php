<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;
use phootwork\collection\Set;

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

	/** @var Set */
	private $acl;
	
	/** @var Set */
	private $l10n;
	
	/** @var Set */
	private $scripts;
	
	/** @var Set */
	private $styles;
	
	/** @var Map */
	private $responder;
	
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

		$this->acl = new Set($data->get('acl', []));
		$this->responder = new Map($data->get('responder', []));
		$this->l10n = new Set($data->get('l10n', []));
		$this->scripts = new Set($data->get('scripts', []));
		$this->styles = new Set($data->get('styles', []));
	}
	
	public function toArray() {
		$arr = [
			'title' => $this->title,
			'description' => $this->description,
			'class' => $this->class,
			'acl' => $this->acl->toArray(),
			'responder' => $this->responder->toArray(),
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
	
	public function setAcl(array $groups) {
		$this->acl->clear();
		$this->acl->addAll($groups);
		return $this;
	}
	
	/**
	 * @return Set
	 */
	public function getAcl() {
		return $this->acl;
	}
	
	/**
	 * Checks whether a responder with the given type is present
	 * 
	 * @param string $type
	 * @return boolean
	 */
	public function hasResponder($type) {
		return $this->responder->has($type);
	}
	
	public function setResponder($type, $class) {
		$this->responder->set($type, $class);
	}
	
	public function getResponder($type) {
		return $this->responder->get($type);
	}
	
	/**
	 * @return Set
	 */
	public function getL10n() {
		return $this->l10n;
	}
	
	/**
	 * @return Set
	 */
	public function getScripts() {
		return $this->scripts;
	}
	
	/**
	 * @return Set
	 */
	public function getStyles() {
		return $this->styles;
	}
}
