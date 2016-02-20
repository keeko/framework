<?php
namespace keeko\framework\routing;

use keeko\core\model\ApplicationUri;
use keeko\core\model\ApplicationUriQuery;
use Symfony\Component\HttpFoundation\Request;
use keeko\framework\exceptions\AppException;

class ApplicationRouter implements RouteMatcherInterface {

	private $destination;

	private $prefix;

	private $uri;

	public function __construct() {
	}

	public function getDestination() {
		return $this->destination;
	}

	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 *
	 * @return ApplicationUri
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 *
	 * @param Request $request
	 * @throws AppException
	 * @return ApplicationUri
	 */
	public function match($request) {
		$uri = null;
		// better loop. Maybe some priority on longer strings?
		// Or strings with more slashes?
		// better query on that?
		$uris = ApplicationUriQuery::create()->joinApplication()->filterByHttphost($request->getHttpHost())->find();
		$found = null;
		
		foreach ($uris as $uri) {
			$uri->setBasepath(rtrim($uri->getBasepath(), '/'));
			$basepath = $uri->getBasepath();
			if ((empty($basepath) && $request->getRequestUri() == '') || (strpos($request->getRequestUri(), $uri->getBasepath()) !== false)) {
				// count slashes
				if ($found === null) {
					$found = $uri;
				} else if (substr_count($uri->getBasepath(), '/') > substr_count($found->getBasepath(), '/')) {
					$found = $uri;
				}
			}
		}

		if ($found === null) {
			throw new AppException(sprintf('No app found on %s', $request->getUri()), 404);
		}

		$this->destination = str_replace($found->getBasepath(), '', $request->getRequestUri());
		$this->prefix = str_replace($request->getBasePath(), '', $found->getBasePath());
		$this->uri = $found;

		return $found;
	}
}
