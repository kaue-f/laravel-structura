# Laravel Structura Skill

## Introduction
You are working in a Laravel project that uses the `kaue-f/laravel-structura` package. This package enforces a clean, consistent, and highly decoupled architecture using Patterns like Actions, Services, DTOs, and Enums. 

Whenever you are asked to generate or refactor business logic, strictly follow these guidelines to maintain architectural consistency.

## 1. Core Architecture & Rules

-   **Actions (`App\Actions`)**: Single-responsibility completely isolated classes.
    -   *Rule:* **Never** call an Action from inside another Action.
    -   *Rule:* Prefer the `Makeable` pattern: Call `MyAction::run(...args)` instead of instantiating.
    -   *Suffix requirement:* Must end with `Action`.
-   **Services (`App\Services`)**: Reusable business logic shared across the application.
    -   *Rule:* Services **can** call other Services.
    -   *Rule:* Use `ServiceResult` for standardized returns (`return ServiceResult::success($data)`).
    -   *Rule:* If using `Makeable` with custom methods, ensure `protected string $makeableMethod` is defined.
    -   *Suffix requirement:* Must end with `Service`.
-   **DTOs - Data Transfer Objects (`App\DTOs`)**: Used to encapsulate data and pass it between layers. 
    -   *Rule:* DTOs should ideally be `readonly` and `final` unless specified otherwise.
    -   *Suffix requirement:* Must end with `DTO` (e.g., `UserRegistrationDTO`).
-   **Enums (`App\Enums`)**: Constant and status definitions.
    -   *Rule:* Use `toData()` for frontend payloads. By default it is minimalist (`id`, `name`). Use `toData(color: true, icon: true)` only if needed.
    -   *Rule:* For custom mapping, use the `map` parameter in `toData()` with keys, field names, or Closures.
    -   *Rule:* Implement labels, colors, and icons using Attributes.
    -   *Suffix requirement:* Must end with `Enum`.

## 2. Naming Conventions & Suffixes

The package dynamically enforces strict naming conventions. If you do not provide a suffix, the package or architectural expectation will append it. Always append the correct suffix in your code explanations and generations:
-   **Action:** `*Action`
-   **Service:** `*Service`
-   **DTO:** `*DTO`
-   **Enum:** `*Enum`
-   **Helper:** `*Helper`
-   **Cache:** `*Cache`

## 3. Configuration Awareness

Before generating fundamental shifts in directories, investigate the `config/structura.php` file (if present). It contains the source of truth for:
*   Namespaces (`App\Actions`, `App\Services`, etc.)
*   File creation paths
*   Default options (e.g., if DTOs should omit `readonly`, or if Services should have a `__construct`)
*   Custom suffixes

## 4. Commands Integration

If the user requests to create a new layer file, use the package's generator commands:
*   `php artisan structura:action Name` (Supports `--transaction` and defaults to `--makeable`)
*   `php artisan structura:service Name` (Supports `--method`, `--result`, and `--makeable`)
*   `php artisan structura:dto Name`
*   `php artisan structura:enum Name` (Supports `--label`, `--trait`)

*(Note: The commands automatically apply the correct suffixes based on `config/structura.php`)*.

---
**Summary for AI agents:**
When solving logic requests, strictly separate layers. Orchestrate with `*Action::run()`, process logic with `*Service`, return `ServiceResult`, and pass data via `*DTO`. Use Enums with `toData()` for UI consistency. Never duplicate logic; use provided package traits to keep classes clean.
