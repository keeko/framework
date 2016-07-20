<?php
namespace keeko\framework\translation;

use keeko\framework\service\ServiceContainer;

class LocaleService {

	/**
	 * The locale, default is 'en'
	 *
	 * @var string
	 */
	private $locale = 'en';

	/** @var ServiceContainer */
	private $service;

	public function __construct(ServiceContainer $service) {
		$this->service = $service;
	}

	public function getLocale() {
		return $this->locale;
	}

	public function setLocale($locale) {
		$this->locale = $locale;
	}

	/**
	 * Finds a localized file by working down the language-tag using the apps
	 * associated locale and finally english as fallback
	 *
	 * @param string $file resource path, must contain {locale} as placeholder
	 * @return string
	 */
	public function findLocaleFile($file) {
		$app = $this->service->getKernel()->getApplication();
		$locale = $app->getLocalization()->getLocale();
		$repo = $this->service->getResourceRepository();

		$workDownLanguageTag = function($locale, $next) use ($repo, $file) {
			// check if the locale has more than one subtag to work down
			if (strpos($locale, '-') === false) {
				return null;
			}

			// drop the last subtag
			$locale = implode('-', array_slice(explode('-', $locale), 0, -1));
			$filename = str_replace('{locale}', $locale, $file);
			if (!$repo->contains($filename)) {
				$filename = $next($locale, $next);
			}
			return $filename;
		};

		$filename = $workDownLanguageTag($locale, $workDownLanguageTag);

		if ($filename === null) {
			$filename = str_replace('{locale}', 'en', $file);
			if (!$repo->contains($filename)) {
				$filename = null;
			}
		}

		return $filename;
	}

	/**
	 * Load a locale file into the translator
	 *
	 * @param string $file
	 * @param string $domain
	 */
	public function loadLocaleFile($file, $domain) {
		$filename = $this->findLocaleFile($file);

		if ($filename !== null) {
			$locale = $this->getLocaleFromFilename($filename);
			$translator = $this->service->getTranslator();
			$translator->addResource('json', $filename, $locale, $domain);
		}
	}

	/**
	 * Returns the locale from a given filename. Filenames are expected in the format:
	 * /<vendor>/<name>/locales/{locale}/<tail>
	 *
	 * @param string $filename
	 * @return string
	 */
	private function getLocaleFromFilename($filename) {
		$matches = [];
		preg_match('#^/[^\/]+\/[^\/]+\/locales\/([^\/]+)\/.*#i', $filename, $matches);
		return $matches[1];
	}
}
