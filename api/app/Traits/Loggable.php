<?php

namespace App\Traits;

use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

trait Loggable
{
    protected $logger;

    protected function getLogger(): Logger
    {
        if (! $this->logger) {
            $this->logger = $this->configureLogger();
        }

        return $this->logger;
    }

    protected function configureLogger(): Logger
    {
        // Get class name for channel
        $channel = strtolower(class_basename($this));
        $logger = new Logger($channel);

        $handler = new StreamHandler(storage_path("logs/{$channel}.log"));

        if (app()->environment('production')) {
            $handler->setFormatter(new JsonFormatter);
        } else {
            $dateFormat = 'Y-m-d H:i:s';
            $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            $formatter = new LineFormatter($output, $dateFormat);
            $handler->setFormatter($formatter);
        }

        $logger->pushHandler($handler);

        return $logger;
    }

    protected function enrichContext(array $context = []): array
    {
        return array_merge($context, [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'class' => get_class($this),
        ]);
    }

    public function logInfo(string $message, array $context = []): void
    {
        $this->getLogger()->info($message, $this->enrichContext($context));
    }

    public function logError(string $message, array $context = []): void
    {
        $this->getLogger()->error($message, $this->enrichContext($context));
    }

    // i can add more logs heer like (warning ...) just fro test i will keep it like this
}
