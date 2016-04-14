<?php
namespace keeko\framework\service;

use \Twig_Environment;
use \Twig_SimpleFunction;
use Puli\Discovery\Api\Discovery;
use Puli\Repository\Api\ResourceRepository;
use Puli\TwigExtension\PuliExtension;
use Puli\TwigExtension\PuliTemplateLoader;
use Puli\UrlGenerator\Api\UrlGenerator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Translation\Loader\JsonFileLoader;
use keeko\framework\kernel\AbstractKernel;
use keeko\framework\foundation\PackageManager;
use keeko\framework\foundation\ModuleManager;
use keeko\framework\auth\AuthManager;
use keeko\framework\preferences\PreferenceLoader;
use keeko\framework\security\Firewall;

class ServiceContainer {

	/** @var PackageManager */
	private $packageManager;
	
	/** @var ModuleManager */
	private $moduleManager;
	
	/** @var AuthManager */
	private $authManager;
	
	/** @var PreferenceLoader */
	private $preferenceLoader;
	
	/** @var Firewall */
	private $firewall;
	
	/** @var KeekoTranslator */
	private $translator;
	
	/** @var EventDispatcher */
	private $dispatcher;
	
	/** @var AbstractKernel */
	private $kernel;
	
	/** @var Twig_Environment */
	private $twig;
	
	/** @var Puli\GeneratedPuliFactory */
	private $puliFactory;
	
	/** @var ResourceRepository */
	private $resourceRepository;
	
	/** @var Discovery */
	private $resourceDiscovery;
	
	/** @var UrlGenerator */
	private $urlGenerator;
	
	/** @var ExtensionRegistry */
	private $extensionRegistry;
	
	public function __construct(AbstractKernel $kernel) {
		$this->kernel = $kernel;
	}
	
	/**
	 * Returns the kernel
	 *
	 * @return AbstractKernel
	 */
	public function getKernel() {
		return $this->kernel;
	}
	
	/**
	 * Returns the event dispatcher
	 *
	 * @return EventDispatcher
	 */
	public function getDispatcher() {
		if ($this->dispatcher === null) {
			$this->dispatcher = new EventDispatcher();
		}
		
		return $this->dispatcher;
	}
	
	/**
	 * Returns the package manager
	 *
	 * @return PackageManager
	 */
	public function getPackageManager() {
		if ($this->packageManager === null) {
			$this->packageManager = new PackageManager($this);
		}
		
		return $this->packageManager;
	}
	
	/**
	 * Returns the module manager
	 *
	 * @return ModuleManager
	 */
	public function getModuleManager() {
		if ($this->moduleManager === null) {
			$this->moduleManager = new ModuleManager($this);
		}
		
		return $this->moduleManager;
	}
	
	/**
	 * Returns the auth manager
	 *
	 * @return AuthManager
	 */
	public function getAuthManager() {
		if ($this->authManager === null) {
			$this->authManager = new AuthManager();
		}
	
		return $this->authManager;
	}
	
	/**
	 * Returns the preference loader
	 *
	 * @return PreferenceLoader
	 */
	public function getPreferenceLoader() {
		if ($this->preferenceLoader === null) {
			$this->preferenceLoader = new PreferenceLoader();
		}
		
		return $this->preferenceLoader;
	}
	
	/**
	 * Returns the firewall
	 *
	 * @return Firewall
	 */
	public function getFirewall() {
		if ($this->firewall === null) {
			$this->firewall = new Firewall($this);
		}
		
		return $this->firewall;
	}
	
	/**
	 * Returns the keeko translation service
	 *
	 * @return KeekoTranslator
	 */
	public function getTranslator() {
		// TODO: how to get the language
		if ($this->translator === null) {
			$app = $this->getKernel()->getApplication();
			$lang = $app->getLocalization()->getLanguage()->getAlpha2();
			$this->translator = new KeekoTranslator($this, $lang);
			$this->translator->addLoader('json', new JsonFileLoader());
			$this->translator->setFallbackLocales(['en']);
		}
		
		return $this->translator;
	}
	
	/**
	 *
	 * @return Puli\GeneratedPuliFactory
	 */
	private function getPuliFactory() {
		if ($this->puliFactory === null) {
			$factoryClass = PULI_FACTORY_CLASS;
			$this->puliFactory = new $factoryClass();
		}
		return $this->puliFactory;
	}
	
	/**
	 * Returns an instance to the puli repository
	 *
	 * @return ResourceRepository
	 */
	public function getResourceRepository() {
		if ($this->resourceRepository === null) {
			$this->resourceRepository = $this->getPuliFactory()->createRepository();
		}
		
		return $this->resourceRepository;
	}
	
	/**
	 * Returns an instance to the puli discovery
	 *
	 * @return Discovery
	 */
	public function getResourceDiscovery() {
		if ($this->resourceDiscovery === null) {
			$repo = $this->getResourceRepository();
			$this->resourceDiscovery = $this->getPuliFactory()->createDiscovery($repo);
		}
		
		return $this->resourceDiscovery;
	}
	
	/**
	 * Returns the url generator for puli resources
	 *
	 * @return UrlGenerator
	 */
	public function getUrlGenerator() {
		if ($this->urlGenerator === null) {
			$discovery = $this->getResourceDiscovery();
			$this->urlGenerator = $this->getPuliFactory()->createUrlGenerator($discovery);
		}
		
		return $this->urlGenerator;
	}

	/**
	 * Returns twig
	 *
	 * @return Twig_Environment
	 */
	public function getTwig() {
		if ($this->twig === null) {
			$repo = $this->getResourceRepository();
			$loader = new PuliTemplateLoader($repo);
			$this->twig = new Twig_Environment($loader);
				
			// puli extension
			$generator = $this->getUrlGenerator();
			$this->twig->addExtension(new PuliExtension($repo, $generator));
	
			// translator function
			$translator = $this->getTranslator();
			$trans = function($key, $params = [], $domain = null) use ($translator) {
				return $translator->trans($key, $params, $domain);
			};
			$this->twig->addFunction(new Twig_SimpleFunction('t', $trans));
		
			// firewall
			$firewall = $this->getFirewall();
			$this->twig->addFunction(new Twig_SimpleFunction('hasPermission', function ($module, $action) use ($firewall) {
				return $firewall->hasPermission($module, $action);
			}));
		}
		
		return $this->twig;
	}
	
	/**
	 * Returns the extension registry
	 *
	 * @return ExtensionRegistry
	 */
	public function getExtensionRegistry() {
		if ($this->extensionRegistry === null) {
			$this->extensionRegistry = new ExtensionRegistry();
		}
		
		return $this->extensionRegistry;
	}
}