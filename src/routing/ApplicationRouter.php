<?php
namespace keeko\framework\routing;

use keeko\core\model\ApplicationUri;
use keeko\core\model\ApplicationUriQuery;
use Symfony\Component\HttpFoundation\Request;
use keeko\framework\exceptions\AppException;
use phootwork\lang\Text;

class ApplicationRouter implements RouteMatcherInterface {

	private $destination;

	private $uri;

	public function __construct() {
	}

	public function getDestination() {
		return $this->destination;
	}

	/**
	 *
	 * @param Request $request
	 * @throws AppException
	 * @return ApplicationUri
	 */
	public function match($request) {
		$found = null;
		$uris = ApplicationUriQuery::create()
			->joinApplication()
			->filterByHttphost($request->getHttpHost())
			->find();

		$requestUri = Text::create($request->getRequestUri())->trimRight('/');
		foreach ($uris as $uri) {
			$basepath = new Text($uri->getBasepath());

			// either request uri and uri basepath are both empty
			// or request uri starts with basepath
			if (($basepath->isEmpty() && $uri->getHttphost() == $request->getHttpHost())
					|| $requestUri->startsWith($basepath)) {
				// assign when it's the first found
				if ($found === null) {
					$found = $uri;
				}

				// count slashes of previously found vs newly found
				else if ($basepath->count('/') > Text::create($found->getBasepath())->count('/')) {
					$found = $uri;
				}
			}
		}

		if ($found === null) {
			throw new AppException(sprintf('No app found on %s', $request->getUri()), 404);
		}

		$this->destination = str_replace($found->getBasepath(), '', $request->getRequestUri());

		return $found;
	}
}
