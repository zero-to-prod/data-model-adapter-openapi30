# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Is

A PHP library that adapts OpenAPI 3.0 component schemas into the `Components` data structure consumed by `zero-to-prod/data-model-generator`. It converts schemas into PHP classes (models) and backed string enums. Part of the zero-to-prod data modeling ecosystem.

## Commands

```bash
# Setup (creates .env from .env.example)
sh dock init
sh dock composer update

# Run tests (uses PHP_VERSION from .env)
sh dock test
sh dock test --filter=MyTest

# Run tests across all PHP versions (8.1–8.5)
sh test.sh

# Composer operations
sh dock composer require some/package
sh dock composer update

# Shell into a container
sh dock php8.3 bash
```

The `dock` script reads `PHP_VERSION` from `.env` and routes to the corresponding Docker service. PHP versions 8.1–8.5 are supported via separate Docker build targets.

## Architecture

Three source files in `src/`:

- **`OpenApi30.php`** — Single static method `adapt(array $open_api_30_schema): Components`. This is the core adapter. It parses the schema via `OpenApi::from()`, then iterates `components.schemas` to produce `Models` (objects) and `Enums` (string enums). Key processing order within the loop:
  1. Top-level enum detection (`type: string` + `enum` array) → adds to `$Enums`, continues
  2. Object schemas → creates constants for each property, then maps properties with type resolution
  3. Property-level concerns: array-of-objects (`$ref` in `items`), inline property enums, `$ref` resolution (with enum suffix detection), and fallback to `PropertyTypeResolver`

- **`Resolvers/PropertyTypeResolver.php`** — Static resolver that merges types from `oneOf`/`anyOf`, handles nullable, and maps `$ref` to class names via `Classname::generate()`.

- **`Helpers/DataModel.php`** — Convenience trait combining `DataModel`, `Transformable`, and `DataModelHelper`.

## Test Structure

Tests live in `tests/` with a `TestCase` base that creates/cleans a `tests/generated/` directory per test.

- **Acceptance tests** (`tests/Acceptance/`): Each has a `schema.json` fixture and a test that calls `OpenApi30::adapt()` → `Engine::generate()` → asserts the generated PHP file content.
- **Unit tests** (`tests/Unit/`): Direct assertions on `adapt()` return values.

When adding a new feature or fixing a bug, follow the acceptance test pattern: create a `schema.json` fixture, generate output, and assert against expected PHP code.

## Key Conventions

- Enum filenames get an `Enum` suffix (e.g., `RoleEnum.php`). When a `$ref` points to an enum schema, the resolved type must also include this suffix.
- Constants are generated for each object property (used as array keys).
- The `Classname::generate()` utility from `zero-to-prod/psr4-classname` converts schema names to PSR-4 class names.
- CI runs backwards compatibility checks via Roave BC Check. Breaking changes require a major version bump.

## Known Bug Patterns (see FIXES.md)

Two bugs were previously fixed and documented in `FIXES.md`. Both involve enum handling:
1. Top-level enum schemas were generated as empty classes instead of enums
2. `$ref` to enum schemas resolved to the wrong type name (missing `Enum` suffix)

Be aware of these patterns when modifying enum-related logic.