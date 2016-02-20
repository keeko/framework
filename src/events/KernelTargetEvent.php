<?php
namespace keeko\framework\events;

use keeko\framework\kernel\KernelTargetInterface;
use Symfony\Component\EventDispatcher\Event;

class KernelTargetEvent extends Event {
	
	const BEFORE_RUN = 'core.kernel.before_run';
	const AFTER_RUN = 'core.kernel.after_run';

	/** @var KernelInterface */
	private $target;
	
	public function __construct(KernelTargetInterface $target) {
		$this->target = $target;
	}
	
	/**
	 * Returns the executed target
	 *
	 * @return KernelTargetInterface
	 */
	public function getTarget() {
		return $this->target;
	}
}