<?php
namespace keeko\framework\service;

use \Swift_Mailer;
use \Swift_MailTransport;
use \Swift_Message;
use \Swift_SendmailTransport;
use \Swift_SmtpTransport;
use \Twig_Environment;
use \Twig_Extension_Debug;
use \Twig_SimpleFunction;
use keeko\framework\foundation\ModuleManager;
use keeko\framework\foundation\PackageManager;
use keeko\framework\kernel\AbstractKernel;
use keeko\framework\preferences\PreferenceLoader;
use keeko\framework\preferences\SystemPreferences;
use keeko\framework\security\AuthManager;
use keeko\framework\security\Firewall;
use keeko\framework\translation\KeekoJsonTranslationLoader;
use keeko\framework\translation\KeekoTranslator;
use keeko\framework\translation\LocaleService;
use Puli\Discovery\Api\Discovery;
use Puli\Repository\Api\ResourceRepository;
use Puli\TwigExtension\PuliExtension;
use Puli\TwigExtension\PuliTemplateLoader;
use Puli\UrlGenerator\Api\UrlGenerator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use keeko\framework\kernel\WebKernel;

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

	/** @var LocaleService */
	private $localeService;

	/** @var EventDispatcher */
	private $dispatcher;

	/** @var AbstractKernel */
	private $kernel;

	/** @var Twig_Environment */
	private $twig;

	/** @var ExtensionRegistry */
	private $extensionRegistry;

	/** @var SwiftMailer */
	private $mailer;

	/** @var PuliService */
	private $puliService;

	public function __construct(PuliService $puli = null) {
		if ($puli === null) {
			$this->puliService = new PuliService();
		} else {
			$this->puliService = $puli;
		}
	}

	public function setKernel(WebKernel $kernel) {
		$this->kernel = $kernel;
	}

	/**
	 * Returns the kernel
	 *
	 * @return WebKernel
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
			$this->authManager = new AuthManager($this);
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
		if ($this->translator === null) {
			$locale = $this->getLocaleService()->getLocale();
			$this->translator = new KeekoTranslator($this, $locale);
			$this->translator->addLoader('json', new KeekoJsonTranslationLoader($this));
			$this->translator->setFallbackLocales(['en']);
		}

		return $this->translator;
	}

	public function getLocaleService() {
		if ($this->localeService === null) {
			$this->localeService = new LocaleService($this);
		}

		return $this->localeService;
	}

	/**
	 * Returns an instance to the puli repository
	 *
	 * @return ResourceRepository
	 */
	public function getResourceRepository() {
		return $this->puliService->getResourceRepository();
	}

	/**
	 * Returns an instance to the puli discovery
	 *
	 * @return Discovery
	 */
	public function getResourceDiscovery() {
		return $this->puliService->getResourceDiscovery();
	}

	/**
	 * Returns the url generator for puli resources
	 *
	 * @return UrlGenerator
	 */
	public function getUrlGenerator() {
		return $this->puliService->getUrlGenerator();
	}

	/**
	 * Returns twig
	 *
	 * @return Twig_Environment
	 */
	public function getTwig() {
		if ($this->twig === null) {
			$options = [];
			if (KEEKO_ENVIRONMENT == KEEKO_DEVELOPMENT) {
				$options['debug'] = true;
			}

			$repo = $this->getResourceRepository();
			$loader = new PuliTemplateLoader($repo);
			$this->twig = new Twig_Environment($loader, $options);

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
			$access = function ($module, $action) use ($firewall) {
				return $firewall->hasPermission($module, $action);
			};

			// debug
			if (KEEKO_ENVIRONMENT == KEEKO_DEVELOPMENT) {
				$this->twig->addExtension(new Twig_Extension_Debug());
			}

			$this->twig->addFunction(new Twig_SimpleFunction('can', $access));
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

	/**
	 * Returns the mailer to send emails
	 *
	 * @return Swift_Mailer
	 */
	public function getMailer() {
		if ($this->mailer === null) {
			$prefs = $this->getPreferenceLoader()->getSystemPreferences();
			switch ($prefs->getMailTransport()) {
				case SystemPreferences::MAIL_TRANSPORT_SMTP:
					$transport = new Swift_SmtpTransport($prefs->getSmtpServer(), $prefs->getSmtpPort());
					$transport->setUsername($prefs->getSmtpUsername());
					$transport->setPassword($prefs->getSmtpPassword());
					$encryption = $prefs->getSmtpEncryption();
					if ($encryption != SystemPreferences::SMTP_ENCRYPTION_NONE) {
						$transport->setEncryption($encryption);
					}
					break;

				case SystemPreferences::MAIL_TRANSPORT_SENDMAIL:
					$transport = new Swift_SendmailTransport($prefs->getSendmail());
					break;

				case SystemPreferences::MAIL_TRANSPORT_MAIL:
				default:
					$transport = new Swift_MailTransport();
					break;
			}

			$this->mailer = new Swift_Mailer($transport);
		}

		return $this->mailer;
	}

	/**
	 * Creates a new message, which can be send with a mailer
	 *
	 * @return Swift_Message
	 */
	public function createMessage() {
		$prefs = $this->getPreferenceLoader()->getSystemPreferences();

		$message = new Swift_Message();
		$sender = $prefs->getPlattformEmail();
		if (!empty($sender)) {
			$message->setFrom($prefs->getPlattformEmail(), $prefs->getPlattformName());
		}

		return $message;
	}
}