<?php
namespace keeko\framework\page;

use phootwork\collection\ArrayList;

class Page {

	private $links;
	private $scripts;
	private $metas;

	private $title;
	private $titleSuffix;
	private $titlePrefix;
	private $defaultTitle;

	public function __construct() {
		$this->links = new ArrayList();
		$this->scripts = new ArrayList();
		$this->metas = new ArrayList();
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
		if (is_string($style)) {
			$link = new Link();
			$link->setHref($style);
			$link->setRel('stylesheet');
			$link->setType('text/css');
			$this->links->add($link);
		} else if ($style instanceof Link) {
			$this->links->add($style);
		}
		return $this;
	}

	public function addStyles(array $styles) {
		foreach ($styles as $style) {
			$this->addStyle($style);
		}
		return $this;
	}

	/**
	 * Returns a list of Link objects
	 *
	 * @return ArrayList
	 */
	public function getLinks() {
		return $this->links;
	}

	public function addLink(Link $link) {
		$this->links->add($link);
	}

	public function addScript($script) {
		if (is_string($script)) {
			$tag = new Script();
			$tag->setSrc($script);
			$tag->setType('text/javascript');
			$this->scripts->add($tag);
		} else if ($script instanceof Script) {
			$this->scripts->add($script);
		}
		return $this;
	}

	public function addScripts(array $scripts) {
		foreach ($scripts as $script) {
			$this->addScript($script);
		}
		return $this;
	}

	/**
	 * Returns a list of Script objects
	 * @return ArrayList
	 */
	public function getScripts() {
		return $this->scripts;
	}

	public function addMeta(Meta $meta) {
		$this->metas->add($meta);
	}

	/**
	 * Returns a list of meta objects
	 *
	 * @return ArrayList
	 */
	public function getMetas() {
		return $this->metas;
	}
}