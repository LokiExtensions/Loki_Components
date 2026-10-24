# Project Memory

## CI dependencies
- CI installs `loki/magento2-base` from Packagist (released tags), while local development uses the Loki_Base `main` checkout. Tests relying on unreleased Loki_Base changes pass locally but fail in CI. Tag a new Loki_Base release and raise the constraint in `composer.json` together (e.g. `ancestor_view_model` needed Loki_Base `^1.3.1`).
- `act` is not installed at `/usr/local/bin/act`; verify integration tests locally with `hersir.py integration-tests --filter <TestName>` instead.
