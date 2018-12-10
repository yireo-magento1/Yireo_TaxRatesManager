<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Object_Manager
 */
class Yireo_TaxRatesManager_Object_Manager
{
    /**
     * @param string $className
     * @return object
     * @throws ReflectionException
     */
    public function get(string $className)
    {
        if ($object = $this->getMagentoObject($className)) {
            return $object;
        }

        if ($object = $this->getPreferenceObject($className)) {
            return $object;
        }

        $reflectionClass = new ReflectionClass($className);
        $constructorParameters = $this->getConstructorParameters($reflectionClass);
        return $reflectionClass->newInstanceArgs($constructorParameters);
    }

    /**
     * @param string $className
     * @return object|null
     */
    private function getMagentoObject(string $className)
    {
        if ($className === Mage_Core_Model_App::class) {
            return Mage::app();
        }

        if ($className === Yireo_TaxRatesManager_Api_LoggerInterface::class) {
            if (!$this->isCli()) {
                return $this->get(Yireo_TaxRatesManager_Logger_Messages::class);
            }

            return $this->get(Yireo_TaxRatesManager_Logger_Console::class);
        }
    }

    /**
     * @param string $className
     * @return object|null
     * @throws ReflectionException
     */
    private function getPreferenceObject(string $className)
    {
        $preferences = $this->getPreferences();
        if (isset($preferences[$className])) {
            return $this->get($preferences[$className]);
        }
    }

    /**
     * @return string[]
     */
    private function getPreferences() : array
    {
        return [];
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return array
     * @throws ReflectionException
     */
    private function getConstructorParameters(ReflectionClass $reflectionClass): array
    {
        $constructorParameters = [];
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return $constructorParameters;
        }

        $parameters = $constructor->getParameters();

        foreach ($parameters as $parameter) {
            if ($parameterClass = $parameter->getClass()) {
                $parameter = $this->get((string)$parameterClass->getName());
                $constructorParameters[] = $parameter;
                continue;
            }

            $constructorParameters[] = $parameter->getDefaultValue();
        }

        return $constructorParameters;
    }

    /**
     * @return bool
     */
    private function isCli(): bool
    {
        return (!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() == 'cli'));
    }
}
