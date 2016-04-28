<?php
namespace keeko\framework\utils;

use keeko\framework\service\ServiceContainer;

trait LocaleLoaderTrait {
	
	/**
	 * @return ServiceContainer
	 */
	abstract protected function getServiceContainer();
	
	/**
	 * @param string $file
	 * @param string $domain
	 */
	private function loadLocaleFile($file, $domain) {
		$app = $this->getServiceContainer()->getKernel()->getApplication();
		$locale = $app->getLocalization()->getLocale();
		$translator = $this->getServiceContainer()->getTranslator();
		$repo = $this->getServiceContainer()->getResourceRepository();
		$lang = \Locale::getPrimaryLanguage($locale);
	
		// load locale
		$l10n = str_replace('{locale}', $locale, $file);
		if ($repo->contains($l10n)) {
			$translator->addResource('json', $l10n, $locale, $domain);
		}
	
		// load lang
		if ($lang != $locale) {
			$l10n = str_replace('{locale}', $lang, $file);
				
			if ($repo->contains($l10n)) {
				$translator->addResource('json', $l10n, $lang, $domain);
			}
		}
	}
}