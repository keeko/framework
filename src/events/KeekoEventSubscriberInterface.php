<?php
namespace keeko\framework\events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

interface KeekoEventSubscriberInterface extends EventSubscriberInterface, KeekoEventListenerInterface {

}