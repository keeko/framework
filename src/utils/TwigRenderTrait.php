<?php
namespace keeko\framework\utils;

use Symfony\Component\HttpFoundation\Request;
use keeko\framework\service\ServiceContainer;

trait TwigRenderTrait {
	
	/**
	 * @return ServiceContainer
	 */
	abstract public function getServiceContainer();
	
	private function getGlobalTwigVariables() {
		$request = Request::createFromGlobals();
		$prefs = $this->getServiceContainer()->getPreferenceLoader()->getSystemPreferences();
		$user = $this->getServiceContainer()->getAuthManager()->getUser();
		$app = $this->getServiceContainer()->getKernel()->getApplication();
		return [
			'global' => [
				'plattform_name' => $prefs->getPlattformName(),
				'locations' => [
					'uri' => $request->getUri(),
					'root_url' => $app->getRootUrl(),
					'app_url' => $app->getAppUrl(),
					'app_path' => $app->getAppPath(),
					'destination' => $app->getDestinationPath()
				]
			],
			'user' => $user
		];
	}

	/**
	 * Renders the given twig template with global and given variables
	 *
	 * @param string $name
	 * @param array $variables
	 * @return string the rendered content
	 */
	protected function render($name, $variables = []) {
		$twig = $this->getServiceContainer()->getTwig();
		return $twig->render($name, array_merge($this->getGlobalTwigVariables(), $variables));
	}
}