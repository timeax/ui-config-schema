<?php declare(strict_types=1);

namespace Timeax\ConfigSchema\Contracts;

use Timeax\ConfigSchema\Schema\ConfigSchema;

interface ProvidesConfigSchema
{
    public function configSchema(): ConfigSchema;
}