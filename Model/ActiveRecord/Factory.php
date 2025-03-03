<?php

namespace M2E\Temu\Model\ActiveRecord;

class Factory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function getObject($modelName)
    {
        // fix for Magento2 sniffs that forcing to use ::class
        $modelName = str_replace('_', '\\', $modelName);

        $model = $this->objectManager->create('\M2E\Temu\Model\\' . $modelName);

        if (!$model instanceof \M2E\Temu\Model\ActiveRecord\AbstractModel) {
            throw new \M2E\Temu\Model\Exception\Logic(
                __('%1 doesn\'t extends \M2E\Temu\Model\ActiveRecord\AbstractModel', $modelName)
            );
        }

        return $model;
    }

    public function getObjectLoaded($modelName, $value, $field = null, $throwException = true)
    {
        try {
            return $this->getObject($modelName)->load($value, $field);
        } catch (\M2E\Temu\Model\Exception\Logic $e) {
            if ($throwException) {
                throw $e;
            }

            return null;
        }
    }
}
