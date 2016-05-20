<?php
namespace keeko\framework\routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

abstract class AbstractRouter {

	private $defaultOptions = [
		'param-separator' => '?',
		'param-delimiter' => '&',
		'param-true' => 'on',
		'param-false' => 'off'
	];

	private $requiredOptions = [
// 		'module',
		'basepath'
	];

	private $optionalOptions = [];

	protected $options;

	/**
	 * The URL matcher
	 *
	 * @var UrlMatcher
	 */
	protected $matcher;

	/**
	 * The URL generator
	 *
	 * @var UrlGenerator
	 */
	protected $generator;
	
	/** @var Request */
	private $request;

	public function __construct(Request $request, array $options) {
		// options
		$resolver = new OptionsResolver();
		$this->configureOptions($resolver);
		$this->options = $resolver->resolve($options);
		$this->request = $request;
	}

	protected function init(RouteCollection $routes) {
		$context = new RequestContext();
		$context->fromRequest($this->request);
		$context->setBaseUrl($this->options['basepath']);
		
		$this->matcher = new UrlMatcher($routes, $context);
		$this->generator = new UrlGenerator($routes, $context);
	}

	private function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array_merge($this->defaultOptions, $this->getDefaultOptions()));
		$resolver->setDefined(array_merge($this->optionalOptions, $this->getOptionalOptions()));
		$resolver->setRequired(array_merge($this->requiredOptions, $this->getRequiredOptions()));
	}
	
	/**
	 * Returns the default options
	 *
	 * @return array
	 */
	protected function getDefaultOptions() {
		return [];
	}
	
	/**
	 * Returns the optional options
	 *
	 * @return array
	 */
	protected function getOptionalOptions() {
		return [];
	}
	
	/**
	 * Returns the required options
	 *
	 * @return array
	 */
	protected function getRequiredOptions() {
		return [];
	}

	/**
	 * Unserializes Parameters
	 *
	 * @param string $params
	 * @return array the unserialized array
	 */
	public function unserializeParams($params) {
		$parts = explode($this->options['param-delimiter'], $params);
		$params = [];
		foreach ($parts as $part) {
			$kv = explode('=', $part);
			if ($kv[0] != '') {
				$value = count($kv) > 1
					? $kv[1] == $this->options['param-true'] ? true
					: ($kv[1] == $this->options['param-false'] ? false : $kv[1]) : true;
				$params = $this->setParam($params, $kv[0], $value);
			}
		}
		
		return $params;
	}
	
	private function setParam($params, $key, $value) {
		// normalize key first
		$key = str_replace(['][', '[', ']'], ['.', '.', ''], $key);
		$parts = explode('.', $key);

		// no array, just set and return
		if (count($parts) == 1) {
			$params[$parts[0]] = $value;
			return $params;
		}

		// is array, go deep
		$node = $parts[0];
		if (!isset($params[$node]) || !is_array($params[$node])) {
			$params[$node] = [];
		}
		array_shift($parts);
		$key = implode('.', $parts);
		$params[$node] = $this->setParam($params[$node], $key, $value);
		return $params;
	}

	/**
	 * Serializes Parameters
	 *
	 * @param array $params
	 * @return string the serialized params
	 */
	public function serializeParams($params) {
		$serialized = '';
		foreach ($params as $key => $val) {
			$serialized .= $key;
			if (is_bool($val) === true) {
				$serialized .= '=' . $val ? $this->options['param-true'] : $this->options['param-false'];
			} else if ($val != '') {
				$serialized .= '=' . $val;
			}
			$serialized .= $this->options['param-delimiter'];
		}
		return $serialized;
	}
}