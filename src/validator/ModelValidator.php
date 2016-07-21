<?php
namespace keeko\framework\validator;

use keeko\framework\service\ServiceContainer;
use keeko\framework\utils\NameUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\ValidatorBuilder;

abstract class ModelValidator implements ValidatorInterface {
	
	/** @var ServiceContainer */
	protected $service;
	
	/** @var ConstraintValidatorFactoryInterface */
	protected $validatorFactory;
	
	/** @var ConstraintViolationListInterface */
	protected $violations;
	
	protected $constraints = [
		'bic' => 'Symfony\Component\Validator\Constraints\Bic',
		'blank' => 'Symfony\Component\Validator\Constraints\Blank',
		'card' => 'Symfony\Component\Validator\Constraints\CardScheme',
		'choice' => 'Symfony\Component\Validator\Constraints\Choice',
		'count' => 'Symfony\Component\Validator\Constraints\Count',
		'country' => 'Symfony\Component\Validator\Constraints\Country',
		'currency' => 'Symfony\Component\Validator\Constraints\Currency',
		'date' => 'Symfony\Component\Validator\Constraints\Date',
		'datetime' => 'Symfony\Component\Validator\Constraints\DateTime',
		'email' => 'Symfony\Component\Validator\Constraints\Email',
		'equal' => 'Symfony\Component\Validator\Constraints\EqualTo',
		'existence' => 'Symfony\Component\Validator\Constraints\Existence',
		'expression' => 'Symfony\Component\Validator\Constraints\Expression',
		'greaterthan' => 'Symfony\Component\Validator\Constraints\GreaterThan',
		'greaterthanorequal' => 'Symfony\Component\Validator\Constraints\GreaterThanOrEqual',
		'iban' => 'Symfony\Component\Validator\Constraints\Iban',
		'identical' => 'Symfony\Component\Validator\Constraints\IdenticalTo',
		'ip' => 'Symfony\Component\Validator\Constraints\Ip',
		'isfalse' => 'Symfony\Component\Validator\Constraints\IsFalse',
		'isnull' => 'Symfony\Component\Validator\Constraints\IsNull',
		'istrue' => 'Symfony\Component\Validator\Constraints\IsTrue',
		'isbn' => 'Symfony\Component\Validator\Constraints\Isbn',
		'issn' => 'Symfony\Component\Validator\Constraints\Issn',
		'language' => 'Symfony\Component\Validator\Constraints\Language',
		'length' => 'Symfony\Component\Validator\Constraints\Length',
		'lessthan' => 'Symfony\Component\Validator\Constraints\LessThan',
		'lessthanorequal' => 'Symfony\Component\Validator\Constraints\LessThanOrEqual',
		'locale' => 'Symfony\Component\Validator\Constraints\Locale',
		'luhn' => 'Symfony\Component\Validator\Constraints\Luhn',
		'notblank' => 'Symfony\Component\Validator\Constraints\NotBlank',
		'notequal' => 'Symfony\Component\Validator\Constraints\NotEqualTo',
		'notidentical' => 'Symfony\Component\Validator\Constraints\NotIdenticalTo',
		'notnull' => 'Symfony\Component\Validator\Constraints\NotNull',
		'optional' => 'Symfony\Component\Validator\Constraints\Optional',
		'range' => 'Symfony\Component\Validator\Constraints\Range',
		'regex' => 'Symfony\Component\Validator\Constraints\Regex',
		'required' => 'Symfony\Component\Validator\Constraints\Required',
		'time' => 'Symfony\Component\Validator\Constraints\Time',
		'type' => 'Symfony\Component\Validator\Constraints\Type',
		'url' => 'Symfony\Component\Validator\Constraints\Url',
		'uuid' => 'Symfony\Component\Validator\Constraints\Uuid'
	];
	
	public function __construct(ServiceContainer $service) {
		$this->service = $service;
		$this->validatorFactory = new ConstraintValidatorFactory();
		$this->violations = new ConstraintViolationList();
	}
	
	abstract protected function getValidations();
	
	public function validate($model) {
		$builder = new ValidatorBuilder();
		$builder->setTranslator($this->service->getTranslator());
		$validator = $builder->getValidator();
		$validations = $this->getValidations();
		$this->violations = new ConstraintViolationList();
		
		foreach ($validations as $column => $validation) {
			$method = 'get' . NameUtils::toStudlyCase($column);
			if (method_exists($model, $method)) {
				$value = $model->$method();
				
				$constraints = [];
				foreach ($validation as $options) {
					$name = $options['constraint'];
					unset($options['constraint']);
					$constraints[] = $this->getConstraint($name, $options);
				}
				
				$violations = $validator->validate($value, $constraints);
				$this->violations->addAll($violations);
			}
		}
		
		return (Boolean) (!(count($this->violations) > 0));
	}
	
	public function getValidationFailures() {
		return $this->violations;
	}

	/**
	 * 
	 * @param Constraint
	 * @return ConstraintValidatorInterface
	 */
	private function getValidator(Constraint $constraint) {
		return $this->validatorFactory->getInstance($constraint);
	}

	/**
	 * 
	 * @param string $name
	 * @return Constraint
	 */
	private function getConstraint($name, $options = []) {
		$constraints = array_merge($this->getCustomConstraints(), $this->constraints);
		
		if (!isset($constraints[$name])) {
			throw new ValidatorException('Constraint ' . $name . ' does not exist');
		}
		
		$className = $constraints[$name];
		if (!class_exists($className)) {
			throw new ValidatorException('Class ' . $className . ' does not exist');
		}
		
		return new $className($options);
	}
	
	/**
	 * Gets custom constraints
	 * 
	 * Expected return value is an array, with name as key and 
	 * fully qualified class name as value:
	 * 
	 * [
	 * 	'email' => 'Symfony\Component\Validator\Constraints\Email'
	 * ]
	 * 
	 */
	protected function getCustomConstraints() {
		return [];
	}
}