<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;

class CustomizeJsonFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new JsonFormatter(JsonFormatter::BATCH_MODE_NEWLINES, true, true));
        }
    }
}
