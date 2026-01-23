# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.2.35] - 23 January 2026
### Fixed
- Make create() `$data` argument optional
- Allow ViewModelFactory::create() and get()

## [2.2.34] - 20 January 2026
### Fixed
- Implement lazy updating of components
- Implement `lazy_update` as component property
- Allow overriding `lazy_load` and `lazy_update` via XML layout
- Based AlpineJS elementId upon `GetElementId` logic
- Unify element IDs based on blocks
- Fix integration tests

## [2.2.33] - 12 January 2026
### Fixed
- Add new GitHub Action workflows
- Check for AlpineJS loader block instead of module being enabled
- Try fix PHPStan issues
- Fixes
- Lower PHPStan level from 3 to 2
- A type-hinting for PHPStan
- Copy generic CI/CD files
- Copy generic CI/CD files

## [2.2.32] - 06 January 2026
### Fixed
- Only load Alpine if `MageOS_AlpineLoader is` disabled

## [2.2.31] - 15 December 2025
### Fixed
- Order component targets by XML layout `render_order` (if there at all)
- Allow blocks without proper DOM DI to be shipped with DOM ID anyway

## [2.2.30] - 12 December 2025
### Fixed
- Fix bug when component has empty output like `<div></div>`

## [2.2.29] - 11 December 2025
### Fixed
- Fix InvalidCharacterError because of `@` in HTML root element
- First tag match was never used

## [2.2.28] - 11 December 2025
### Fixed
- Move dependency ComponentViewModelInterface from `Loki_Base` to `Loki_Components`

## [2.2.27] - 11 December 2025
### Fixed
- Move childRenderer class from `Loki_Components` to `Loki_Base`
- Do not convert HTML attributes like `@` to `x-on` when updating HTML
- Pass layout to `loki_components_repository_post_dispatch`
- Convert update error into ComponentUpdate value object
- Pass updates to event `loki_components_repository_post_dispatch` to give context
- Add debug message to component update
- Trigger `loki_components_repository_post_dispatch` event after repository updates are done

## [2.2.26] - 02 December 2025
### Fixed
- Replace `@` with `x-on:` and `:` with `x-bind:` when updating HTML attributes
- Properly switch HTML attributes of root node when updating via AJAX

## [2.2.25] - 27 November 2025
### Fixed
- Make sure generated element ID contains only alphanumeric plus dash

## [2.2.24] - 21 November 2025
### Fixed
- Move AJAX check towards RequiredValidator class
- Move Integration Test assertion away from setUp into separate assertion method
- Replace `$block->getChildHtml()` with `$childRenderer->all()` including better sorting

## [2.2.23] - 14 November 2025
### Fixed
- Add FilterScope to every Filter
- Add component and scope args to FilterInterface::filter()

## [2.2.22] - 12 November 2025
### Fixed
- Undo removal of M2.4.5 workarounds, use patch instead
- Make components visible by default
- Rename LokiMessageStore to Message
- Fix unit tests

## [2.2.21] - 04 November 2025
### Fixed
- Make sure required numerical value is valid when zero

## [2.2.20] - 04 November 2025
### Fixed
- Fix typo

## [2.2.19] - 04 November 2025
### Fixed
- Apply new dispatching to admin controller too

## [2.2.18] - 03 November 2025
### Fixed
- Update composer keywords
- Fetch component by blockId via `Alpine.store('components').getComponentByBlockId()`
- Cleanup of debugging statements
- Fix broken test
- Allow target to be any block, not just components
- Add component repository repository so when bulking, country is saved first
- Simplify request data via AJAX and fix issue with edit URL
- New container to allow for CSS prop and child sorting

## [2.2.17] - 22 October 2025
### Fixed
- Prevent `preg_replace` from removing slashes in x-loki-init script
- Do not escape `$css()` with `escapeHtmlAttr()` but `escapeHtml()`
- Skin down LokiHtmlReplacer a bit
- Add LokiComponentExtender.addMixin()
- Add LokiComponentExtender for mixins and other modifications
- Newlines

## [2.2.16] - 17 October 2025
### Fixed
- Optimize AJAX queue and HTML updater
- Move LokiHtmlUpdater from LokiCheckout to `Loki_Components`
- Move all MX logic to separate module `Loki_EmailMxValidator`
- Allow overriding SVG attributes if they already exist
- Formatting

## [2.2.15] - 15 October 2025
### Fixed
- Automatically assign template vars to any template starting with Loki module-prefix
- Allow any Loki-driven block to use Loki template variables

## [2.2.14] - 10 October 2025
### Fixed
- Apply proper ARIA attributes to tab components

## [2.2.13] - 08 October 2025
### Fixed
- Add dev-mode warning when rendering of sorted childeren fails

## [2.2.12] - 08 October 2025
### Fixed
- Allow sorting of children via block argument "sort_order"

## [2.2.11] - 08 October 2025
### Fixed
- Allow any component to have a :cssClass

## [2.2.10] - 07 October 2025
### Fixed
- Support icons passed as asset ID

## [2.2.9] - 07 October 2025
### Fixed
- Register activeTabId when switching
- Make sure required values that are empty are validated
- Remove option "validate_on_ajax" and simply allow empty required values on-load

## [2.2.8] - 06 October 2025
### Fixed
- Wrap CSS classes in `$css()`
- Add disabled state for active tab

## [2.2.7] - 01 October 2025
### Fixed
- Improve image rendering

## [2.2.6] - 30 September 2025
### Fixed
- Restructure of methods of imageRenderer to make more sense
- Add `x-ignore` to SVG output

## [2.2.5] - 29 September 2025
### Fixed
- Fix wrong escaping

## [2.2.4] - 29 September 2025
### Fixed
- Fix wrong escaping

## [2.2.3] - 29 September 2025
### Fixed
- Fix wrong escaping in JS

## [2.2.2] - 29 September 2025
### Fixed
- Fix wrong escaping in JS

## [2.2.1] - 29 September 2025
### Fixed
- Optimize image rendering
- Prevent child renderer exception when child is empty
- Fix PHPStan issue with setTemplate() being called

## [2.2.0] - 23 September 2025
### Added
- Add Loki Component exception as complex message
- Output SVG with given XML attributes properly
- New ImageRenderer::icon() method
- Add new block variable "imageRenderer"
- Move new login component to `LokiCheckout_Core` and beautify

### Fixed
- Enhance modals under Luma a bit
- Remove redundant CSS classes from icon containers
- Fix block rendering of static blocks
- Move messages timeout configuration from `Loki_Components` to `Loki_Base`
- Fuse observers to avoid ordering conflict
- Implement new blockRenderer and childRenderer arguments
- Remove block argument from templateRenderer
- Configure block prefixes via DI type
- Only allow Loki block variables on block starting with "loki"
- Rename loki.script from container to block to allow caching
- Change containers into blocks to allow for caching
- Rename Alpine stores
- Add `.prevent` modifier to `@click` event handler
- Rename Alpine store checkout to LokiCheckout, components to LokiComponents
- Rewrite transfer of global messages from components to be a lot simpler
- Allow steps to have no block yet (outside of checkout)

## [2.1.4] - 17 September 2025
### Fixed
- Fix new JSON strucure of component updates in admin controller

## [2.1.3] - 16 September 2025
### Fixed
- Make showLoaderTimeout in components configurable
- Make AJAX queue interval configurable via XML layout
- Remove duplicate targets in AJAX queue
- Simplify messaging in LokiCheckout components
- Add todo
- Refresh stored messages when global components are refreshed
- Move LokiComponents global messages to regular messages template
- Add new Loki_Base as dependency and move over common logic
- Move AJAX behaviour into separate LokiAjaxQueue
- Rename loki-components.alpinejs to loki.alpinejs
- Add x-title only in Developer Mode
- Simplify active tab selection
- Create generic PHTML template for tab-buttons
- Fix strlen issue
- Allow a component to send validation messages globally via dispatch_local_messages=false
- Simplify security filter because the loop is taken care off by the Filter class
- Fix merge conflict
- Cleanup event listener
- Remove LengthBehaviourInterface

## [2.1.2] - 04 September 2025
### Fixed
- Fix unit test
- Add MagentoVersion util for later usage

## [2.1.1] - 03 September 2025
### Fixed
- Move LokiFieldViewModelImageOutput to LokiComponentsUtilImageOutput to remove circular dependency
- Copy generic CI/CD files

## [2.1.0] - 02 September 2025
### Added
- Refactor hard-coded field attributes to FieldViewModel::getFieldAttributes()

### Fixed
- Remove LengthBehaviourInterface entirely
- Add usage instructions to README

## [2.0.15] - 29 August 2025
### Fixed
- Add dep
- Add recurring setup to check for modules to enable

## [2.0.14] - 27 August 2025
### Fixed
- Make sure modal does not cause issue if there is no modal element
- Convert all DOM classes and IDs to lowercase; Only validate components once
- Add comment with global message

## [2.0.13] - 26 August 2025
### Fixed
- Make sure to log exceptions although they are caught

## [2.0.12] - 21 August 2025
### Fixed
- New ViewModel `AppMode`
- Remove obsolete call to LokiComponentsFocusListener
- Move scripts from top of body to bottom of body
- Remove old `focus-listener`
- Add dep with `Loki_CssUtils`
- Import right CssClass util
- Fix newlines after comments
- Declare used PHP namespaces
- Add escaping of template code
- Add missing `strict_types` declaration
- Move CssClass and CssStyle to separate package
- Use `xmark.svg` to close global messages

## [2.0.11] - 18 August 2025
### Fixed
- Allow for PHP 8.1 compatibility
- Lower requirements to PHP 8.1
- Option to limit cart items in sidebar but collapse to entire list
- Rename tab in Store Config from "Yireo" to "Loki"

## [2.0.10] - 15 August 2025
### Fixed
- Add new `$style()` variable in PHTML templates
- Move config from Yireo tab to Loki tab

## [2.0.9] - 13 August 2025
### Fixed
- Add data-valid attribute to fields
- Add addLocalMessage helpers

## [2.0.8] - 10 August 2025
### Fixed
- Move loading features into separate component and component partial
- Lift up to PHPStan level 3
- Add escaping to templates

## [2.0.7] - 07 August 2025
### Fixed
- Lift up to PHPStan level 2
- Prevent null HTML in transport from breaking AddHtmlAttributesToComponentBlock observer

## [2.0.6] - 06 August 2025
### Fixed
- Move initMethods and destroyMethods into component partials
- Rename LokiDataLoaderComponentPartial to LokiLoadDataComponentPartial
- Implement `aria-errormessage` together with existing `aria-invalid`
- Do not display container of local messages, if there are no local messages
- Set message area default to local
- Allow XML layout to set message area to make component messages global or local
- Lower PHP requirement to PHP 8.2+

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

