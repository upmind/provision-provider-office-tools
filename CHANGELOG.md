# Changelog

All notable changes to the package will be documented in this file.

## [v1.2.0](https://github.com/upmind/provision-provider-office-tools/releases/tag/v1.2.0) - 2026-05-07

- Add optional `webmail_url` to Titan configuration
- Include optional `control_panel_url` and `workspace_url` in InfoResult

## [v1.1.0](https://github.com/upmind/provision-provider-office-tools/releases/tag/v1.1.0) - 2026-04-09

- Add `type` to LoginResult to support either a redirect URL or an access token
  - Add `login_result_type` to Titan configuration to specify which login result type to return

## [v1.0.3](https://github.com/upmind/provision-provider-office-tools/releases/tag/v1.0.3) - 2026-01-14

- Add optional `invoice_number` to BillingParams; use as orderId in Titan create()

## [v1.0.2](https://github.com/upmind/provision-provider-office-tools/releases/tag/v1.0.2) - 2026-01-14

- Update Titan renew() and changePackage() API calls to include modifyMailOrder action parameter

## [v1.0.1](https://github.com/upmind/provision-provider-office-tools/releases/tag/v1.0.1) - 2025-05-26

- Make `billing.expiry_date` optional

## [v1.0.0](https://github.com/upmind/provision-provider-office-tools/releases/tag/v1.0.0) - 2025-05-25

- Initial public release
