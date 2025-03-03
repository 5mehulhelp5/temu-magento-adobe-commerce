<?php

namespace M2E\Temu\Model\Instruction\Handler;

interface HandlerInterface
{
    public function process(Input $input);
}
