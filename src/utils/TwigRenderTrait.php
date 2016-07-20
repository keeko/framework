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
			],
			'locations' => [
				'uri' => $request->getUri(),
				'root_url' => $prefs->getRootUrl(),
				'account_url' => $prefs->getAccountUrl(),
				'developer_url' => $prefs->getDeveloperUrl(),
				'api_url' => $prefs->getApiUrl(),
				'app_url' => $app->getBaseUrl(),
				'destination' => $app->getDestination()
			],
			'user' => $user,
			'page' => $app->getPage()
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