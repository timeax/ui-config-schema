<?php declare(strict_types=1);

namespace Timeax\ConfigSchema\Schema;

use JsonSerializable;

final readonly class ConfigField implements JsonSerializable, ConfigNode
{
    /**
     * @param array<int,string> $rules
     * @param array<int,ConfigOption> $options
     * @param array<string,mixed> $meta
     */
    public function __construct(
        public string  $name,
        public string  $label,
        public string  $type = 'text',
        public bool    $required = false,
        public bool    $secret = false,
        public array   $rules = [],
        public mixed   $default = null,
        public ?string $helpText = null,
        public array   $options = [],
        public bool    $sandbox = false,
        public array   $meta = [],

        /** Group path like "gateway" or "gateway.credentials" (optional). */
        public ?string $group = null,
    )
    {
    }

    public function nodeType(): string
    {
        return 'field';
    }

    public function withGroup(?string $group): self
    {
        return new self(
            name: $this->name,
            label: $this->label,
            type: $this->type,
            required: $this->required,
            secret: $this->secret,
            rules: $this->rules,
            default: $this->default,
            helpText: $this->helpText,
            options: $this->options,
            sandbox: $this->sandbox,
            meta: $this->meta,
            group: $group,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label,
            'type' => $this->type,
            'required' => $this->required,
            'secret' => $this->secret,
            'rules' => $this->rules,
            'default' => $this->default,
            'helpText' => $this->helpText,
            'name' => $this->name,
            'options' => array_map(
                static fn(ConfigOption $o) => $o->jsonSerialize(),
                $this->options
            ),
            'sandbox' => $this->sandbox,
            'meta' => $this->meta,

            // Optional, but useful for round-trips.
            'group' => $this->group,
        ];
    }
}