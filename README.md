````md
# Timeax UI Config Schema

Portable, framework-agnostic primitives for describing **UI configuration forms** (fields, options, rules, secrets) and returning **structured validation results**.

This package is meant to be shared across SDKs and host apps so they can expose a consistent “config UI schema” without coupling to any specific UI framework (React/Vue/etc.) or product domain (payments, plugins, etc.).

---

## Installation

```bash
composer require timeax/ui-config-schema
````

---

## What this package provides

### Schema primitives

* **`ConfigSchema`**: a collection of config fields (live + sandbox aware)
* **`ConfigField`**: a single input definition (type, label, rules, default, help text, options, meta)
* **`ConfigOption`**: discrete option values for selects/radios

### Config values container

* **`ConfigBag`**: holds `options` + `secrets`, and can filter itself to only keys allowed by a schema for the current mode (live vs sandbox)

### Validation results

* **`ConfigValidationResult`**: consistent shape for validation outcomes + field errors
* **`ConfigValidationError`**: a single field-level error record

### Optional contract

* **`ProvidesConfigSchema`**: a tiny interface for anything that can expose a `ConfigSchema`

---

## Package goals

* **UI-agnostic:** describes inputs, not components.
* **Domain-agnostic:** can power payment gateway configs, plugin configs, feature toggles, etc.
* **Secret-safe defaults:** config serialization is public by default (secrets are excluded).
* **Host-friendly:** a host can store both live + sandbox keys together, while runtime selects the active subset.

---

## Quick example

### 1) Define a schema

```php
<?php

use Timeax\ConfigSchema\Schema\ConfigField;
use Timeax\ConfigSchema\Schema\ConfigOption;
use Timeax\ConfigSchema\Schema\ConfigSchema;

$schema = new ConfigSchema([
    new ConfigField(
        name: 'public_key',
        label: 'Public Key',
        required: true,
        helpText: 'Your provider public key',
        sandbox: false,
    ),

    new ConfigField(
        name: 'secret_key',
        label: 'Secret Key',
        required: true,
        secret: true,
        helpText: 'Never expose this to the client',
        sandbox: false,
    ),

    new ConfigField(
        name: 'mode',
        label: 'Mode',
        type: 'select',
        required: true,
        options: [
            new ConfigOption('card', 'Card'),
            new ConfigOption('bank', 'Bank Transfer'),
        ],
        sandbox: false,
    ),

    // Sandbox/test keys (separate field set)
    new ConfigField(
        name: 'test_public_key',
        label: 'Test Public Key',
        required: true,
        sandbox: true,
    ),

    new ConfigField(
        name: 'test_secret_key',
        label: 'Test Secret Key',
        required: true,
        secret: true,
        sandbox: true,
    ),
]);
```

### 2) Store config values with options + secrets

```php
<?php

use Timeax\ConfigSchema\Support\ConfigBag;

$config = new ConfigBag(
    sandbox: true,
    options: [
        'mode' => 'card',
        'test_public_key' => 'pk_test_...',
    ],
    secrets: [
        'test_secret_key' => 'sk_test_...',
    ],
);
```

### 3) Filter values by schema for the active mode

This is useful when a host stores both live and test keys in one record, but runtime wants only the active subset.

```php
<?php

$filtered = $config->filterBySchema($schema);

$filtered->option('test_public_key'); // available
$filtered->secret('test_secret_key'); // available

// not included (wrong mode)
$filtered->option('public_key');      // null
$filtered->secret('secret_key');      // null
```

### 4) Return a validation result (structured errors)

```php
<?php

use Timeax\ConfigSchema\Support\ConfigValidationResult;

$result = ConfigValidationResult::fail()
    ->addError('test_public_key', 'Required')
    ->addError('test_secret_key', 'Required');

if (! $result->isOk()) {
    // consistent error format for API responses
    return $result->jsonSerialize();
}
```

The serialized format looks like:

```json
{
  "ok": false,
  "errors": {
    "test_public_key": [{ "field": "test_public_key", "message": "Required", "code": null }],
    "test_secret_key": [{ "field": "test_secret_key", "message": "Required", "code": null }]
  }
}
```

---

## Contract usage (optional)

If you want a consistent way for drivers/modules to expose their config schema:

```php
<?php

use Timeax\ConfigSchema\Contracts\ProvidesConfigSchema;
use Timeax\ConfigSchema\Schema\ConfigSchema;

final class SomeModule implements ProvidesConfigSchema
{
    public function configSchema(): ConfigSchema
    {
        // return schema...
    }
}
```

---

## Notes on secrets

* `ConfigBag::jsonSerialize()` intentionally excludes secrets.
* Use `option()` for non-sensitive keys, `secret()` for sensitive keys.
* The `ConfigField::$secret` flag allows UIs to render secret inputs accordingly, but the **host** is still responsible for never exposing secrets.

---

## License

MIT

```
```
