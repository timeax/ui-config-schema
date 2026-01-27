<?php declare(strict_types=1);

namespace Timeax\ConfigSchema\Schema;

use JsonSerializable;

final readonly class ConfigField implements JsonSerializable
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
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'required' => $this->required,
            'rules' => $this->rules,
            'secret' => $this->secret,
            'default' => $this->default,
            'helpText' => $this->helpText,
            'options' => array_map(
                static fn(ConfigOption $o) => $o->jsonSerialize(),
                $this->options
            ),
            'sandbox' => $this->sandbox,
            'meta' => $this->meta,
        ];
    }
}