<?php
namespace keeko\framework\kernel;

use keeko\core\model\Application;
use Symfony\Component\HttpFoundation\Request;
use keeko\framework\installer\InstallerApplication;

class InstallerKernel extends AbstractKernel {

	public function process(array $options = []) {
		try {
			$steps = isset($options['steps']) ? $options['steps'] : ['setup'];
			
			$uri = '';
			$locale = InstallerApplication::DEFAULT_LOCALE;
			if (in_array('setup', $steps)) {
				$uri = $options['uri'];
				$locale = isset($options['locale']) ? $options['locale'] : $locale;
			} else if (KEEKO_DATABASE_LOADED) {
				$prefs = $this->service->getPreferenceLoader()->getSystemPreferences();
				$uri = $prefs->getRootUrl();
			}
			
			$request = Request::create($uri);
			$request->setDefaultLocale(InstallerApplication::DEFAULT_LOCALE);
			$request->setLocale($locale);

			$model = new Application();
			$model->setName('keeko/core/installer');
			
			$app = new InstallerApplication($model, $this->service, $options['io']);
			$this->app = $app;

			$response = $this->handle($app, $request);
			
			return $response;
		} catch (\Exception $e) {
			printf('<b>[%s] %s</b><pre>%s</pre>', get_class($e), $e->getMessage(), $e->getTraceAsString());
		}
	}
	
	/**
	 * Returns the application
	 *
	 * @return InstallerApplication
	 */
	public function getApplication() {
		return $this->app;
	}
}
