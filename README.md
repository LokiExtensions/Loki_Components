# Loki_Components

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/loki/magento2-components)
<!-- badges.specs.end -->


**This is the main Magento 2 module for Loki Components, as is being used by the Loki Checkout suite. Loki Components are a combination of Alpine.js (JavaScript) and PHP (Magento): The package offers enhanced Alpine.js components that automate AJAX calls to be handled in the backend, complete with filtering, validation, updating multiple HTML elements at once, and much more.**

## Installation
Install this package via composer:

```bash
composer require loki/magento2-components
```

Next, enable this module:
```bash
bin/magento module:enable Loki_Components
```
## Usage
See [loki-extensions.com/docs/components](https://loki-extensions.com/docs/components)

## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_Components/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_Components/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_Components/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_Components/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/LokiExtensions/Loki_Components/compile.yml?label=compile)
<!-- badges.test.end -->
