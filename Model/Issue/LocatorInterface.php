<?php

namespace M2E\Temu\Model\Issue;

interface LocatorInterface
{
    /**
     * @return DataObject[]
     */
    public function getIssues(): array;
}
