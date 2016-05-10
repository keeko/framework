<?php
namespace keeko\framework\events;

use keeko\framework\kernel\KernelHandleInterface;
use Symfony\Component\EventDispatcher\Event;

class KernelHandleEvent extends Event {
	
	const PRE_RUN = 'framework.kernel.pre_run';
	const POST_RUN = 'framework.kernel.post_run';

	/** @var KernelInterface */
	private $target;
	
	public function __construct(KernelHandleInterface $target) {
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