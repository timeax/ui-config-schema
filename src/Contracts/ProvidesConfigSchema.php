<?php declare(strict_types=1);

namespace Timeax\ConfigSchema\Contracts;

use Timeax\ConfigSchema\Schema\ConfigSchema;
use Timeax\ConfigSchema\Schema\UiConfigSchema;
use Timeax\ConfigSchema\Support\ConfigBag;
use Timeax\ConfigSchema\Support\ConfigValidationResult;

interface ProvidesConfigSchema
{
    public function configSchema(): ?ConfigSchema;

    public function uiConfigSchema(): ?UiConfigSchema;

    public function validateConfig(?ConfigBag $config = null): ConfigValidationResult;

    public function publicConfig(?ConfigBag $config = null): array;

    public function redactForLogs(mixed $payload): mixed;
}