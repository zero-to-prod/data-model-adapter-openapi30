# Zerotoprod\DataModelAdapterOpenapi30

[![Repo](https://img.shields.io/badge/github-gray?logo=github)](https://github.com/zero-to-prod/data-model-adapter-openapi30)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/zero-to-prod/data-model-adapter-openapi30/test.yml?label=tests)](https://github.com/zero-to-prod/data-model-adapter-openapi30/actions)
[![Packagist Downloads](https://img.shields.io/packagist/dt/zero-to-prod/data-model-adapter-openapi30?color=blue)](https://packagist.org/packages/zero-to-prod/data-model-adapter-openapi30/stats)
[![Packagist Version](https://img.shields.io/packagist/v/zero-to-prod/data-model-adapter-openapi30?color=f28d1a)](https://packagist.org/packages/zero-to-prod/data-model-adapter-openapi30)
[![License](https://img.shields.io/packagist/l/zero-to-prod/data-model-adapter-openapi30?color=red)](https://github.com/zero-to-prod/data-model-adapter-openapi30/blob/main/LICENSE.md)
[![wakatime](https://wakatime.com/badge/github/zero-to-prod/data-model-adapter-openapi30.svg)](https://wakatime.com/badge/github/zero-to-prod/data-model-adapter-openapi30)
[![Hits-of-Code](https://hitsofcode.com/github/zero-to-prod/data-model-adapter-openapi30?branch=main)](https://hitsofcode.com/github/zero-to-prod/data-model-adapter-openapi30/view?branch=main)

## Installation
You can install this package via Composer.

```shell
composer require zero-to-prod/data-model-adapter-openapi30
```

## Local Development

This project provides a convenient [dock](https://github.com/zero-to-prod/dock) script to simplify local development workflows within Docker containers.

You can use this script to initialize the project, manage Composer dependencies, and run tests in a consistent PHP environment.

### Prerequisites

- Docker installed and running
- A `.env` file (created automatically via the `dock init`z command, if it doesn’t already exist)

### Initializing

Use the following commands to set up the project:

```shell
sh dock init
sh dock composer update
```

### Testing

This command runs PHPUnit inside the Docker container, using the PHP version specified in your `.env` file.
You can modify or extend this script to include additional tests or commands as needed.

```shell
sh dock test
```

Run the test suite with all versions of php:

```shell
sh test.sh
```

### Configuration

Before starting development, verify that your `.env` file contains the correct settings.

You can specify which PHP version to use for local development, debugging, and Composer operations by updating these variables in your `.env` file:

```dotenv
PHP_VERSION=8.1
PHP_DEBUG=8.1
PHP_COMPOSER=8.1
```

Make sure these values reflect the PHP versions you intend to use.
If the `.env` file does not exist, run the `sh dock init` command to create one from the `.env.example` template.

## Contributing

Contributions, issues, and feature requests are welcome!
Feel free to check the [issues](https://github.com/zero-to-prod/dynamic-setter/issues) page if you want to contribute.

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Commit changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Create a new Pull Request.
