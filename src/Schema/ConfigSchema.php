<?php declare(strict_types=1);

namespace Timeax\ConfigSchema\Schema;

use JsonSerializable;

final readonly class ConfigSchema implements JsonSerializable
{
    /** @param array<int,ConfigField> $fields */
    public function __construct(public array $fields = [])
    {
    }

    /**
     * Return a schema containing only fields matching the requested sandbox mode.
     */
    public function forSandbox(bool $sandbox): self
    {
        $fields = array_values(array_filter(
            $this->fields,
            static fn(ConfigField $f): bool => $f->sandbox === $sandbox
        ));

        return new self($fields);
    }

    public function forLive(): self
    {
        return $this->forSandbox(false);
    }

    public function forTest(): self
    {
        return $this->forSandbox(true);
    }

    /** @return array<int,string> field names for the requested sandbox mode */
    public function keysForSandbox(bool $sandbox): array
    {
        $out = [];
        foreach ($this->fields as $f) {
            if ($f->sandbox === $sandbox) {
                $out[] = $f->name;
            }
        }
        return $out;
    }

    public function jsonSerialize(): array
    {
        return [
            'fields' => array_map(
                static fn(ConfigField $f) => $f->jsonSerialize(),
                $this->fields
            ),
        ];
    }
}