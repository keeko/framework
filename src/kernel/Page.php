<?php
namespace keeko\framework\kernel;

use phootwork\collection\ArrayList;

class Page {

	private $styles;
	private $scripts;
	
	private $title;
	private $titleSuffix;
	private $titlePrefix;
	private $defaultTitle;
	
	public function __construct() {
		$this->styles = new ArrayList();
		$this->scripts = new ArrayList();
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getDefaultTitle() {
		return $this->defaultTitle;
	}
	
	/**
	 * @param string $title
	 */
	public function setDefaultTitle($title) {
		$this->defaultTitle = $title;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitleSuffix() {
		return $this->titleSuffix;
	}

	/**
	 * @param string $titleSuffix
	 */
	public function setTitleSuffix($titleSuffix) {
		$this->titleSuffix = $titleSuffix;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitlePrefix() {
		return $this->titlePrefix;
	}

	/**
	 * @param string $titlePrefix
	 */
	public function setTitlePrefix($titlePrefix) {
		$this->titlePrefix = $titlePrefix;
		return $this;
	}
	
	public function getFullTitle() {
		if ($this->title === null) {
			return $this->defaultTitle;
		}
		return trim($this->titlePrefix . ' ' . $this->title . ' ' . $this->titleSuffix);
	}
	
	public function addStyle($style) {
		if (!$this->styles->contains($style)) {
			$this->styles->add($style);
		}
		return $this;
	}
	
	public function hasStyle($style) {
		return $this->styles->contains($style);
	}
	
	public function removeStyle($style) {
		$this->styles->remove($style);
		return $this;
	}
	
	public function getStyles() {
		return $this->styles->toArray();
	}
	
	public function addScript($script) {
		if (!$this->scripts->contains($script)) {
			$this->scripts->add($script);
		}
		return $this;
	}
	
	public function hasScript($script) {
		return $this->scripts->contains($script);
	}
	
	public function removeScript($script) {
		$this->scripts->remove($script);
		return $this;
	}
	
	public function getScripts() {
		return $this->scripts->toArray();
	}
}