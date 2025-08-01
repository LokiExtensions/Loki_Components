# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.5] - 01 August 2025
### Fixed
- Add CSS wrapper in various templates
- Make sure child block counter is used by all renderers
- Move modal close button into separate PHTML
- Trim HTML before trying to detect HTML elements within

## [2.0.4] - 30 July 2025
### Fixed
- Special characters should not be converted to HTML chars
- Move logic to helper methods
- Implement better abort handling for AJAX requests
- Cancel existing AJAX calls on subsequent requests

## [2.0.3] - 29 July 2025
### Fixed
- Remove ugly PHPUnit 10 work-around of getTestResultObject()

## [2.0.2] - 28 July 2025
### Fixed
- Add router for increased performance

## [2.0.1] - 24 July 2025
### Fixed
- Allow closing modal by clicking outside of modal

## [2.0.0] - 21 July 2025
### Fixed
- Rename PHP namespace from `Yireo_LokiComponents` to `Loki_Components`
- Rename composer from `yireo/magento2-loki-components` to `loki/magento2-components`

## [1.0.9] - 16 July 2025
### Fixed
- Add helper methods for adding notices, warnings, errors and success messages

## [1.0.8] - 07 July 2025
### Fixed
- Add addGlobalError helper method to base component
- Do not convert special chars in field values (example `'s Hertogenbosch`)

## [1.0.7] - 18 June 2025
### Fixed
- Add details/summary to popup message
- WIP on maps integration
- Remove echo from controller output, just make debug message more readable
- Enhance workflow of errors during final stage
- Reuse currentUri in AJAX request

## [1.0.6] - 22 May 2025
### Fixed
- Fix z-index of messages
- Add simple LokiForm component
- Update admin settings with tooltip and regenerate new MODULE.json

## [1.0.5] - 13 May 2025
### Fixed
- Fix possible warning
- Fix possible warning
- Add allFunctionsCalledOnLoad

## [1.0.4] - 07 May 2025
### Fixed
- CSP issue with closing messages
- Move data loading into separate component partial
- Rewrite Alpine from initActions object to methods starting with init
- Allow for plugins to be loaded right before main Alpine
- Improve styling of messages in admin
- Move Loki messages in admin to page.messages container
- Add admin controller
- Support security for complex values
- Modules should NOT determine the page layout for reusable handles

## [1.0.3] - 01 May 2025
### Fixed
- Allow everything to happen in backend as well
- Allow PHP 8.4 in CI
- Fix issue with LokiCheckoutMollie DI type overriding core validators

## [1.0.2] - 28 April 2025
### Fixed
- Check for integration test containing string, not full match

## [1.0.1] - 25 April 2025
### Fixed
- Add `setValue()` method
- CSP fixes

## [1.0.0] - 24 April 2025
- Major version to promote stability, because it works!

### Fixed
- Intercept unwanted exceptions while rendering
- Move from `x-init-data` to separate `text/x-loki-init` script to prevent possible escaping issues
- Fix possible issue when LokiCheckout config is used outside of checkout
- Allow for `jsData` to be set from block as well
- Fix CSP issue with new `x-json` directive
- Do not mark `ComponentUtil` as a whole as deprecated
- Move all field behaviour to FieldComponentType
- Move tabs into new component partial
- Make loader icon in fields depend on Alpine and activate only after configurable timeout

## [0.0.18] - 16 April 2025
### Fixed
- Chop up modal into regular component, Loki Component and component partial
- Move scripts from "before.body.end" to new "loki-scripts" container
- Complete modal functionality
- Use `js_component_name` from block by default

## [0.0.17] - 08 April 2025
### Fixed
- Simplify reloading of this component
- Simplify loading state of target components
- Remove select-icon when loading select-field

## [0.0.16] - 04 April 2025
### Fixed
- Reset HTML attributes when reloading component

## [0.0.15] - 04 April 2025
### Fixed
- Allow for specific exceptions to redirect back to checkout/cart
- Refactor to ignore non-existing target rendering
- Rename default CSS class from "inline" to "default"
- Only show HTML hints for failing blocks in Developer Mode
- Refactor way that loading is handled a bit
- First batch of Playwright functional tests
- Add new MODULE.json with meta-information
- Fix test for components that are disallowed rendering
- Add proper styling of messages under Luma

## [0.0.14] - 11 March 2025
### Fixed
- Turn Phrases into strings automatically
- Add validators `date` and `past_date`
- Reorganize tests
- Add module dependencies
- Huge refactoring to move logic into new LokiFieldComponents module
- Intercept non-existing target error
- Apply `document.getElementById` after nextTick
- Improve handling of AJAX errors
- Make sure to remove loader when fatal errors occurs on server
- Remove wrong scope in CSS
- Add various integration tests 
- Add TargetRenderer test
- Cleanup layout loader and add test
- Fix integration test of translation strings
- Abstract ViewModel methods for length behaviour
- Move EmailValidatorTest
- Add missing Dutch translations
- Properly translate validation messages
- Move email availability in separate validator `email_available`
- Rename `block loki-checkout.defaults.x` to `loki-components.defaults.x`
- Rename `loki-checkout.css_classes` to `loki-components.css_classes`
- Config option for MX lookup was using wrong path
- Rewrite `Alpine.store()` APIs

## [0.0.13] - 25 February 2025
### Fixed
- Standardize JS event names
- Rename yireo-loki-checkout.component-change to loki-components.component.update
- Add generic LokiComponentsLogger
- StepForwardButton not activated after component updates ($nextTick is now used)
- Hide entire global messages div if empty

## [0.0.12] - 24 February 2025
### Fixed
- Remove obsolete NoBlockFoundException

## [0.0.11] - 24 February 2025
### Fixed
- Setting for disabling MX lookup for email validator
- Destroy components before updating their HTML

## [0.0.10] - 24 February 2025
### Fixed
- Allow multiple destroy actions

## [0.0.9] - 24 February 2025
### Fixed
- Remove old hasChanges method
- Do not display NoComponentFoundException on frontend
- Improve autofill mechanism

## [0.0.8] - 24 February 2025
### Fixed
- Default target definitions were dropped when cache was refreshed from non-frontend
- Add little hint about microseconds
- Listen to autofill changes
- Add support default value

## [0.0.7] - 24 February 2025
### Fixed
- Implement focus listener in a better way

## [0.0.6] - 23 February 2025
### Fixed
- Allow stack trace to be shown as global message while debugging
- Position global messages fixed in top
- Add new setting for timeout of global messages
- Only add trace to exceptions if debugging is enabled
- Do not switch back to originalValue, preventing AJAX loop
- Only validate AJAX calls by default

## [0.0.5] - 22 February 2025
### Fixed
-  Fix issue with HTML attributes for nested components
-  Prevent duplicate HTML attributes

## [0.0.4] - 20 February 2025
### Fixed
- Re-add getFilters and getValidators differently
- Only validate AJAX calls setting
- Rewrite updating of HTML and component props
- Remove duplicate newlines for easier debugging
- Add failsafe checks

### Added
- Add debug config option

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

