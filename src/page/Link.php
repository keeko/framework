<?php
namespace keeko\framework\page;

class Link {

	/** @var string */
	private $href;

	/** @var string */
	private $type;

	/** @var string */
	private $rel;

	/** @var string */
	private $integrity;

	/** @var string */
	private $media;

	/** @var string */
	private $title;

	/**
	 *
	 * @return string
	 */
	public function getHref() {
		return $this->href;
	}

	/**
	 *
	 * @param string $href
	 */
	public function setHref($href) {
		$this->href = $href;
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
	public function getRel() {
		return $this->rel;
	}

	/**
	 *
	 * @param string $rel
	 */
	public function setRel($rel) {
		$this->rel = $rel;
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
	public function getMedia() {
		return $this->media;
	}

	/**
	 *
	 * @param string $media
	 */
	public function setMedia($media) {
		$this->media = $media;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 *
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}



}