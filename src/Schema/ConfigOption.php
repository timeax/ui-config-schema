<?php declare(strict_types=1);

namespace Timeax\ConfigSchema\Schema;

use JsonSerializable;

final readonly class ConfigOption implements JsonSerializable
{
    public function __construct(
        public string|int $value,
        public string $label,
    ) {}

    public function jsonSerialize(): array
    {
        return ['value' => $this->value, 'label' => $this->label];
    }
}