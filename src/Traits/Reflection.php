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
     * @return \ReflectionProperty $property
     */
    private function getAccessibleReflectionProperty(string $propertyName) {

        $property = $this->getReflectionObject()->getParentClass()->getProperty($propertyName);
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
     * @return mixed
     */
    public function getReflectionProperty(string $propertyName) {

        $property = $this->getAccessibleReflectionProperty($propertyName);

        return $property->getValue($this);
    }

    /**
     * Set ReflectionObject property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return void
     */
    public function setReflectionProperty(string $propertyName, $value) {

        $property = $this->getAccessibleReflectionProperty($propertyName);
        $property->setValue($this, $value);
    }
}
