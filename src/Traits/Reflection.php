<?php

namespace MediactiveDigital\MedKit\Traits;

trait Reflection {

    /**
     * @var \ReflectionObject
     */
    private $reflectionObject;

    /**
     * Get ReflectionObject based on current class
     *
     * @return \ReflectionObject
     */
    private function getReflectionObject() {

        $this->reflectionObject = isset($this->reflectionObject) ? $this->reflectionObject : new \ReflectionObject($this);

        return $this->reflectionObject;
    }

    /**
     * Get accessible ReflectionObject method
     *
     * @param string $methodName
     * @return \ReflectionMethod $method
     */
    private function getAccessibleReflectionMethod(string $methodName) {

        $method = $this->getReflectionObject()->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Get accessible ReflectionObject property
     *
     * @param string $propertyName
     * @param bool $self
     * @return \ReflectionProperty $property
     */
    private function getAccessibleReflectionProperty(string $propertyName, bool $self = false) {

        $reflectionObject = $this->getReflectionObject();
        $parentReflectionObject = $self ? false : $reflectionObject->getParentClass();

        while ($parentReflectionObject && !$parentReflectionObject->hasProperty($propertyName)) {

            $parentReflectionObject = $parentReflectionObject->getParentClass();
        }

        $property = $parentReflectionObject ? $parentReflectionObject->getProperty($propertyName) : $reflectionObject->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Call ReflectionObject method
     *
     * @param string $methodName
     * @param string[] $arguments
     * @return mixed
     */
    public function callReflectionMethod(string $methodName, ...$arguments) {

        $method = $this->getAccessibleReflectionMethod($methodName);
        
        return $method->invokeArgs($this, $arguments);
    }

    /**
     * Get ReflectionObject property
     *
     * @param string $propertyName
     * @param bool $self
     * @return mixed
     */
    public function getReflectionProperty(string $propertyName, bool $self = false) {

        $property = $this->getAccessibleReflectionProperty($propertyName, $self);

        return $property->getValue($this);
    }

    /**
     * Set ReflectionObject property
     *
     * @param string $propertyName
     * @param mixed $value
     * @param bool $self
     * @return void
     */
    public function setReflectionProperty(string $propertyName, $value, bool $self = false) {

        $property = $this->getAccessibleReflectionProperty($propertyName, $self);
        $property->setValue($this, $value);
    }
}
