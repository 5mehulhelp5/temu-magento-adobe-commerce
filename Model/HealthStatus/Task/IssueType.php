<?php

namespace M2E\Temu\Model\HealthStatus\Task;

abstract class IssueType extends AbstractModel
{
    public const TYPE = 'issue';

    public function getType()
    {
        return self::TYPE;
    }

    public function mustBeShownIfSuccess()
    {
        return false;
    }
}
