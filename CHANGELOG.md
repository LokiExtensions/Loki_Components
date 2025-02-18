# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.0.3] - 18 February 2025
### Fixed
- Friendlier message for unknown email domain in validator

### Refactor
- Refactor LocalMessageRegistry methods
- Remove obsolete ViewModel methods getFilters and getValidators

## [0.0.2] - 13 February 2025
### Fixed
- Fix rendering of global messages
- Fix issue in refreshing other targets
- Fix autofocus after HTML updates
- Redirect to cart when AJAX fails with empty quote
- Fix `preg_match` issue
- Allow for default core messages to be used as Loki GlobalMessages
- Fix message location for Luma
- Improve logic of AJAX handling
- Pass original request through to layout

### Added
- Move CssClass to LokiComponents

### Refactor
- Remove dependency between validators and component object

## [0.0.1] - 18 January 2025
- Initial release
