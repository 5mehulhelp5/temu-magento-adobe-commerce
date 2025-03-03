<?php

namespace M2E\Temu\Model\HealthStatus\Task;

abstract class AbstractModel
{
    public function mustBeShownIfSuccess()
    {
        return true;
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return Result
     */
    abstract public function process();
}
