<?php
namespace keeko\framework\service;

use phootwork\collection\Stack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\Translator;
use keeko\framework\events\KernelTargetEvent;

class KeekoTranslator extends Translator implements EventSubscriberInterface {

	private $domain;
	
	/** @var Stack */
	private $domainStack;
	
	/** @var ServiceContainer */
	private $service;
	
	public function __construct(ServiceContainer $service, $locale) {
		parent::__construct($locale);
		$this->service = $service;
		
		$dispatcher = $service->getDispatcher();
		$dispatcher->addSubscriber($this);
		
		$this->domainStack = new Stack();
	}
	
	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents() {
		return [
			KernelTargetEvent::BEFORE_RUN => 'beforeRun',
			KernelTargetEvent::AFTER_RUN => 'afterRun'
		];
	}
	
	public function beforeRun(KernelTargetEvent $e) {
		$target = $e->getTarget();
		
		$this->domainStack->push($target->getCanonicalName());
		$this->domain = $this->domainStack->peek();
	}
	
	public function afterRun(KernelTargetEvent $e) {
		$this->domainStack->pop();
		$this->domain = $this->domainStack->peek();
	}
	
	public function trans($id, array $parameters = [], $domain = null, $locale = null) {
		// check if it has a plural
		if (isset($parameters['count'])) {
			$count = $parameters['count'];
			unset($parameters['count']);
			return $this->transChoice($id, $count, $parameters, $domain, $locale);
		}

		// anyway proceed with singular
		$params = $this->prepareParams($parameters);
	
		// set domain if none passed but one is present
		if ($this->domain !== null && $domain === null) {
			$domain = $this->domain;
		}
		
		return parent::trans($id, $params, $domain, $locale);
	}

	public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null) {
		$params = $this->prepareParams($parameters);
		
		// set domain if none passed but one is present
		if ($this->domain !== null && $domain === null) {
			$domain = $this->domain;
		}
		
		return parent::transChoice($id, $number, $params, $domain, $locale);
	}
	
	private function prepareParams(array $parameters) {
		$params = [];
		foreach ($parameters as $key => $value) {
			$params['{' . $key . '}'] = $value;
		}
		
		return $params;
	}
}
