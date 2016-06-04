<?php
namespace keeko\framework\model;

interface TypeInferencerInterface {

	public function getModelClass($type);

	public function getQueryClass($type);
}