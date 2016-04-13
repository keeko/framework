<?php
namespace keeko\framework\utils;

class NameUtils {
	
	private static $pluralizer;

	/**
	 * Transforms a given input into StudlyCase
	 *
	 * @param string $input
	 * @return string
	 */
	public static function toStudlyCase($input) {
		$input = trim($input, '-_');
		return ucfirst(preg_replace_callback('/([A-Z-_][a-z]+)/', function($matches) {
			return ucfirst(str_replace(['-','_'], '',$matches[0]));
		}, $input));
	}
	
	/**
	 * Transforms a given input into camelCase
	 *
	 * @param string $input
	 * @return string
	 */
	public static function toCamelCase($input) {
		return lcfirst(self::toStudlyCase($input));
	}
	
	/**
	 * Transforms a given input into kebap-case
	 *
	 * @param string $input
	 * @return string
	 */
	public static function toKebapCase($input) {
		return self::dasherize($input);
	}

	/**
	 * Transforms a given input into snake_case
	 *
	 * @param string $input
	 * @return string
	 */
	public static function toSnakeCase($input) {
		return str_replace('-', '_', self::dasherize($input));
	}
	
	public static function dasherize($input) {
		return trim(strtolower(preg_replace_callback('/([A-Z _])/', function($matches) {
			$suffix = '';
			if (preg_match('/[A-Z]/', $matches[0])) {
				$suffix = $matches[0];
			}
			return '-' . $suffix;
		}, $input)), '-');
	}
	
	/**
	 * Returns the plural form of the input
	 *
	 * @param string $input
	 * @return string
	 */
	public static function pluralize($input) {
		if (self::$pluralizer === null) {
			self::$pluralizer = new StandardEnglishSingularizer();
		}

		return self::$pluralizer->getPluralForm($input);
	}
	
	/**
	 * Returns the singular form of the input
	 *
	 * @param string $input
	 * @return string
	 */
	public static function singularize($input) {
		if (self::$pluralizer === null) {
			self::$pluralizer = new StandardEnglishSingularizer();
		}
	
		return self::$pluralizer->getSingularForm($input);
	}
}