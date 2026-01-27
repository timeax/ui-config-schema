````md
# Timeax UI Config Schema

Portable, framework-agnostic primitives for describing **UI configuration forms** (fields, options, rules, secrets) and
returning **structured validation results**.

This package is meant to be shared across SDKs and host apps so they can expose a consistent “config UI schema” without
coupling to any specific UI framework (React/Vue/etc.) or product domain (payments, plugins, etc.).

It supports both:

- **Flat schemas** (classic `ConfigSchema` → `ConfigField[]`)
- **Forti-style nested schemas** (tree: `UiConfigSchema` → `settings: { key: ConfigNode }` with `ConfigGroup` +
  `ConfigField`)

---

## Installation

```bash
composer require timeax/ui-config-schema
````

---

## What this package provides

### Forti-style tree schema

* **`UiConfigSchema`**: root container with `settings: array<string, ConfigNode>`
* **`ConfigNode`**: node interface (either a group or a field)
* **`ConfigGroup`**: a group node containing `children: array<string, ConfigNode>`
* **`ConfigField`**: a field node (leaf) — also implements `ConfigNode`

### Flat schema (backwards-compatible)

* **`ConfigSchema`**: a list of `ConfigField` objects

### Options

* **`ConfigOption`**: discrete option values for selects/radios/multiselect, etc.

### Config values container

* **`ConfigBag`**: holds `options` + `secrets`, supports looking up values and filtering by schema (secrets excluded
  from default serialization)

### Validation results

* **`ConfigValidationResult`**: consistent shape for validation outcomes + field errors
* **`ConfigValidationError`**: a single field-level error record

### Optional contract

* **`ProvidesConfigSchema`**: a tiny interface for anything that can expose a schema

---

## Included JSON Schema

This repository ships a Draft-07 JSON Schema that mirrors the Forti-style structure:

* `schema/timeax.ui-config-schema.draft-07.json`

It validates objects shaped like:

```json
{
  "settings": {
    "gateway": {
      "type": "group",
      "label": "Gateway",
      "children": {
        "public_key": {
          "label": "Public Key",
          "type": "text",
          "required": true
        },
        "secret_key": {
          "label": "Secret Key",
          "type": "password",
          "required": true,
          "secret": true
        }
      }
    }
  }
}
```

---

## Quick examples

### 1) Define a Forti-style tree schema

```php
<?php

use Timeax\ConfigSchema\Schema\ConfigField;
use Timeax\ConfigSchema\Schema\ConfigGroup;
use Timeax\ConfigSchema\Schema\UiConfigSchema;
use Timeax\ConfigSchema\Schema\ConfigOption;

$schema = new UiConfigSchema([
    'gateway' => new ConfigGroup(
        label: 'Gateway',
        children: [
            'public_key' => new ConfigField(
                name: 'public_key',
                label: 'Public Key',
                required: true,
            ),
            'secret_key' => new ConfigField(
                name: 'secret_key',
                label: 'Secret Key',
                type: 'password',
                required: true,
                secret: true,
            ),
        ],
    ),

    'mode' => new ConfigField(
        name: 'mode',
        label: 'Mode',
        type: 'select',
        required: true,
        options: [
            new ConfigOption('card', 'Card'),
            new ConfigOption('bank', 'Bank Transfer'),
        ],
    ),
]);
```

### 2) Flatten a tree schema into a flat schema

`flatten()` traverses the tree and returns a `ConfigSchema(fields[])`.

It also stamps each field with its `group` path so the structure can be rebuilt later.

```php
<?php

$flat = $schema->flatten();

// ConfigSchema { fields: ConfigField[] }
// Each field may carry ->group (e.g. "gateway")
```

### 3) Rebuild a Forti-style tree from a flat schema

```php
<?php

use Timeax\ConfigSchema\Schema\ConfigSchema;

/** @var ConfigSchema $flat */
$tree = $flat->toUiConfigSchema();
```

### 4) Store config values with options + secrets

```php
<?php

use Timeax\ConfigSchema\Support\ConfigBag;

$config = new ConfigBag(
    sandbox: true,
    options: [
        'mode' => 'card',
        'public_key' => 'pk_test_...',
    ],
    secrets: [
        'secret_key' => 'sk_test_...',
    ],
);

// secrets are excluded from jsonSerialize by default
$public = $config->jsonSerialize();
```

### 5) Validation result example

```php
<?php

use Timeax\ConfigSchema\Support\ConfigValidationResult;

$result = ConfigValidationResult::fail()
    ->addError('public_key', 'Required')
    ->addError('secret_key', 'Required');

if (! $result->isOk()) {
    return $result->jsonSerialize();
}
```

---

## Notes on secrets

* `ConfigBag::jsonSerialize()` intentionally excludes secrets.
* Use `ConfigField::$secret = true` to mark a field as sensitive.
* Hosts should still enforce secret-handling (mask in UI, avoid logs, encrypt at rest if desired).

---

## License

MIT

```
```
