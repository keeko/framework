<?php
namespace keeko\framework\schema;

use phootwork\collection\ArrayList;
use phootwork\collection\CollectionUtils;
use phootwork\collection\Map;
use phootwork\lang\Arrayable;
use phootwork\lang\Text;

/**
 * @author thomas
 *
 */
class PackageSchema extends RootSchema implements Arrayable {

	/** @var Map */
	private $data;

	/** @var string */
	private $name;

	/** @var string */
	private $vendor;

	/** @var string */
	private $fullName;

	/** @var string */
	private $description;

	/** @var string */
	private $type;

	/** @var string */
	private $license;

	/** @var ArrayList<string> */
	private $keywords;

	/** @var ArrayList<AuthorSchema> */
	private $authors;

	/** @var Map<string, string> */
	private $require;

	/** @var Map<string, string> */
	private $requireDev;

	/** @var AutoloadSchema */
	private $autoload;

	/** @var Map<string, mixed> */
	private $extra;

	/** @var KeekoSchema */
	private $keeko;

	public function __construct($contents = []) {
		$this->parse($contents);
	}

	private function parse($contents) {
		$data = new Map($contents);

		$this->setFullName($data->get('name'));

		$this->description = $data->get('description');
		$this->type = $data->get('type');
		$this->license = $data->get('license');
		$this->keywords = new ArrayList($data->get('keywords', []));

		$this->authors = new ArrayList();
		if ($data->has('authors')) {
			foreach ($data->get('authors') as $authorData) {
				$this->authors->add(new AuthorSchema($authorData));
			}
		}

		$this->autoload = new AutoloadSchema($data->get('autoload', []));
		$this->require = new Map($data->get('require', []));
		$this->requireDev = new Map($data->get('require-dev', []));
		$this->extra = CollectionUtils::toMap($data->get('extra', []));

		$this->keeko = new KeekoSchema($this, $this->extra->get('keeko', []));
		$this->data = $data;
	}

	public function toArray() {
		$authors = [];
		foreach ($this->authors as $author) {
			$authors[] = $author->toArray();
		}

		$keys = ['name', 'description', 'type', 'license', 'keywords', 'authors', 'autoload', 'require', 'require-dev', 'extra'];
		$arr = array_merge(array_flip($keys), $this->data->toArray());

		$arr['name'] = $this->fullName;
		$arr['description'] = $this->description;
		$arr['type'] = $this->type;
		$arr['license'] = $this->license;
		$arr['keywords'] = $this->keywords->toArray();
		$arr['authors'] = $authors;
		$arr['autoload'] = $this->autoload->toArray();
		$arr['require'] = $this->require->toArray();
		$arr['require-dev'] = $this->requireDev->toArray();
		$arr['extra'] = array_map(function ($v) {
			if (is_object($v) && method_exists($v, 'toArray')) {
				return $v->toArray();
			}
			return $v;
		}, $this->extra->toArray());

		$keeko = $this->keeko->toArray();
		if (count($keeko) > 0) {
			$arr['extra']['keeko'] = $keeko;
		}

		if (count($arr['keywords']) == 0) {
			unset($arr['keywords']);
		}

		if (count($arr['extra']) == 0) {
			unset($arr['extra']);
		}

		return $arr;
	}

	/**
	 * Sets the full name (vendor/name) of the package
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setFullName($name) {
		$fullName = new Text($name);

		$this->fullName = $name;
		$this->name = $fullName->substring($fullName->indexOf('/') + 1)->toString();
		$this->vendor = $fullName->substring(0, $fullName->indexOf('/'))->toString();

		return $this;
	}

	/**
	 * Sets the vendor part of the package's full name
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setVendor($vendor) {
		$this->setFullName($vendor . '/' . $this->name);
		return $this;
	}

	/**
	 * Sets the name part of the package's full name
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setName($name) {
		$this->setFullName($this->vendor . '/' . $name);
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function getCleanName() {
		return str_replace(['keeko-', '-app', '-module'], '', $this->name);
	}

	public function getVendor() {
		return $this->vendor;
	}

	public function getFullName() {
		return $this->fullName;
	}

	public function getCanonicalName() {
		return str_replace('/', '.', $this->fullName);
	}

	/**
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 *
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

	/**
	 *
	 * @return the string
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
	 * @return the string
	 */
	public function getLicense() {
		return $this->license;
	}

	/**
	 *
	 * @param string $license
	 */
	public function setLicense($license) {
		$this->license = $license;
		return $this;
	}

	/**
	 *
	 * @return ArrayList<string>
	 */
	public function getKeywords() {
		return $this->keywords;
	}

	/**
	 * @return AutoloadSchema
	 */
	public function getAutoload() {
		return $this->autoload;
	}

	/**
	 * Returns the authors
	 *
	 * @return ArrayList<AuthorSchema>
	 */
	public function getAuthors() {
		return $this->authors;
	}

	/**
	 *
	 * @return Map
	 */
	public function getRequire() {
		return $this->require;
	}

	/**
	 *
	 * @return Map
	 */
	public function getRequireDev() {
		return $this->requireDev;
	}

	/**
	 * @return Map
	 */
	public function getExtra() {
		return $this->extra;
	}

	/**
	 * @return KeekoSchema
	 */
	public function getKeeko() {
		return $this->keeko;
	}

}

