<?php
namespace keeko\framework\page;

class Script {

	/** @var string */
	private $async;

	/** @var string */
	private $defer;

	/** @var string */
	private $integrity;

	/** @var string */
	private $src;

	/** @var string */
	private $type;

	/** @var string */
	private $crossorigin;

	/**
	 *
	 * @return string
	 */
	public function getAsync() {
		return $this->async;
	}

	/**
	 *
	 * @param string $async
	 */
	public function setAsync($async) {
		$this->async = $async;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getDefer() {
		return $this->defer;
	}

	/**
	 *
	 * @param string $defer
	 */
	public function setDefer($defer) {
		$this->defer = $defer;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getIntegrity() {
		return $this->integrity;
	}

	/**
	 *
	 * @param string $integrity
	 */
	public function setIntegrity($integrity) {
		$this->integrity = $integrity;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getSrc() {
		return $this->src;
	}

	/**
	 *
	 * @param string $src
	 */
	public function setSrc($src) {
		$this->src = $src;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 *
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getCrossorigin() {
		return $this->crossorigin;
	}

	/**
	 *
	 * @param string $crossorigin
	 */
	public function setCrossorigin($crossorigin) {
		$this->crossorigin = $crossorigin;
		return $this;
	}


}