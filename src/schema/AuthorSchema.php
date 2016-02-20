<?php
namespace keeko\framework\schema;

use phootwork\collection\Map;
use phootwork\lang\Arrayable;

class AuthorSchema implements Arrayable {

	/** @var string */
	private $name;
	
	/** @var string */
	private $email;
	
	/** @var string */
	private $homepage;
	
	/** @var string */
	private $role;
	
	public function __construct($contents = []) {
		$this->parse($contents);
	}
		
	private function parse($contents) {
		$data = new Map($contents);
	
		$this->name = $data->get('name');
		$this->email = $data->get('email');
		$this->homepage = $data->get('homepage');
		$this->role = $data->get('role');
	}
	
	public function toArray() {
		$arr = ['name' => $this->name];
		
		if (!empty($this->email)) {
			$arr['email'] = $this->email;
		}
		
		if (!empty($this->homepage)) {
			$arr['homepage'] = $this->homepage;
		}
		
		if (!empty($this->role)) {
			$arr['role'] = $this->role;
		}
		
		return $arr;
	}

	/**
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 *
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getHomepage() {
		return $this->homepage;
	}

	/**
	 *
	 * @param string $homepage
	 */
	public function setHomepage($homepage) {
		$this->homepage = $homepage;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 *
	 * @param string $role
	 */
	public function setRole($role) {
		$this->role = $role;
		return $this;
	}

}
