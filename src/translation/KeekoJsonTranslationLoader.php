<?php
namespace keeko\framework\translation;

use keeko\framework\service\ServiceContainer;
use phootwork\file\File;
use phootwork\json\Json;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class KeekoJsonTranslationLoader implements LoaderInterface {

	/** @var ServiceContainer */
	private $service;

	public function __construct(ServiceContainer $service) {
		$this->service = $service;
	}

	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Translation\Loader\LoaderInterface::load()
	 */
	public function load($resource, $locale, $domain = 'messages') {
		$repo = $this->service->getResourceRepository();
		if (!$repo->contains($resource)) {
			throw new NotFoundResourceException(sprintf('File "%s" not found.', $resource));
		}

		// find file in puli repo
		$file = $repo->get($resource);
		$json = $file->getBody();
		$data = Json::decode($json);
		$messages = [];

		// flatten plural strings
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$vals = [];
				foreach ($value as $k => $v) {
					$vals[] = sprintf('%s: %s', $k, $v);
				}
				$val = implode('|', $vals);
			} else {
				$val = $value;
			}

			$messages[$key] = str_replace(['{{', '}}'], '%', $val);
		}

		// put them into message catalog
		$catalogue = new MessageCatalogue($locale);
		$catalogue->add($messages, $domain);

		return $catalogue;
	}
}
