<?php
namespace keeko\framework\schema;

use phootwork\file\File;
use phootwork\file\exception\FileNotFoundException;
use phootwork\json\Json;

abstract class RootSchema {
	
	/**
	 *
	 * @param string $filename
	 * @throws FileNotFoundException
	 * @throws JsonException
	 * @return static
	 */
	public static function fromFile($filename) {
		$file = new File($filename);
	
		if (!$file->exists()) {
			throw new FileNotFoundException(sprintf('File not found at: %s', $filename));
		}
	
		$json = Json::decode($file->read());
	
		return new static($json);
	}
}