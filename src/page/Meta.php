<?php
namespace keeko\framework\page;

class Meta {

	/** @var string */
	private $name;

	/** @var string */
	private $httpEquiv;

	/** @var string */
	private $charset;

	/** @var string */
	private $content;

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
	public function getHttpEquiv() {
		return $this->httpEquiv;
	}

	/**
	 *
	 * @param string $httpEquiv
	 */
	public function setHttpEquiv($httpEquiv) {
		$this->httpEquiv = $httpEquiv;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 *
	 * @param string $charset
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 *
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
		return $this;
	}



}