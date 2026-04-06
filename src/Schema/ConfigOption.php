<?php declare(strict_types=1);

namespace Timeax\ConfigSchema\Schema;

use JsonSerializable;

final readonly class ConfigOption implements JsonSerializable
{
    public ?string $id;

    /**
     * @param array<int,string> $includes
     * @param array<int,string> $excludes
     * @param array<int, ConfigOption> $children
     */
    public function __construct(
        public string|int $value,
        public string     $label,
        ?string           $id = null,
        public array      $includes = [],
        public array      $excludes = [],
        public array      $children = []
    )
    {
        $this->id = $id ?? self::deriveId($value);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'label' => $this->label,
            'includes' => $this->includes,
            'excludes' => $this->excludes,
            'children' => $this->children
        ];
    }

    private static function deriveId(string|int $value): string
    {
        $normalized = strtolower(trim((string)$value));
        $normalized = preg_replace('/[^a-z0-9]+/', '-', $normalized) ?? '';
        $normalized = trim($normalized, '-');

        return $normalized !== '' ? $normalized : 'option';
    }
}
