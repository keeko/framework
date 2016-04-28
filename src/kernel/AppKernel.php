<?php
namespace keeko\framework\kernel;

use keeko\framework\routing\ApplicationRouter;
use Symfony\Component\HttpFoundation\Request;

class AppKernel extends AbstractKernel {

	public function process(array $options = []) {
		try {
			$request = Request::createFromGlobals();

// 			printf('<p>Basepath: %s<br>
// 					Pathinfo: %s<br>
// 					Baseurl: %s<br>
// 					Host: %s<br>
// 					HttpHost: %s<br>
// 					Requsturi: %s<br>
// 					Uri: %s<br>
// 					Port: %s<br>
// 					Secure: %s</p>',
// 					$request->getBasePath(),
// 					$request->getPathInfo(),
// 					$request->getBaseUrl(),
// 					$request->getHost(),
// 					$request->getHttpHost(),
// 					$request->getRequestUri(),
// 					$request->getUri(),
// 					$request->getPort(),
// 					$request->isSecure() ? 'yes' : 'no');
		
			// no trailing slashes in urls
			if (substr($request->getUri(), -1) == '/') {
				$this->redirect(rtrim($request->getUri(), '/'));
			}
		
			$router = new ApplicationRouter();
		
			$uri = $router->match($request);
			$model = $uri->getApplication();
			$destination = str_replace($router->getDestination(), '', $request->getUri());
			$root = str_replace($router->getPrefix(), '', $destination);
		
			$class = $model->getClassName();
			$app = new $class($model, $this->service);
			$app->setLocalization($uri->getLocalization());
			$app->setRootUrl($root);
			$app->setAppPath($router->getPrefix());
			$app->setDestinationPath($router->getDestination());
			$this->app = $app;

			$response = $this->handle($app, $request);

			if ($response instanceof RedirectResponse) {
				$response->sendHeaders();
				$this->redirect($response->getTargetUrl());
			}
			
			$response->prepare($request);
			
			return $response;
		} catch (\Exception $e) {
			printf('<b>[%s] %s</b><pre>%s</pre>', get_class($e), $e->getMessage(), $e->getTraceAsString());
		}
	}

	private function redirect($url) {
		header('Location: ' . $url);
		exit(0);
	}
}
