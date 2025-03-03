<?php

namespace M2E\Temu\Model\Issue\Notification;

use M2E\Temu\Model\Issue\DataObject;

interface ChannelInterface
{
    /**
     * @param DataObject $message
     *
     * @return void
     */
    public function addMessage(DataObject $message): void;
}
