# Laravel Structura Skill

## Introduction
You are working in a Laravel project that uses the `kaue-f/laravel-structura` package. This package enforces a clean, consistent, and highly decoupled architecture using patterns like Actions, Services, DTOs, Enums, Caches, Helpers, and Traits.

Whenever you are asked to generate or refactor business logic, strictly follow these guidelines to maintain architectural consistency.

## 1. Core Architecture & Rules

-   **Actions (`App\Actions`)**: Single-responsibility classes that orchestrate specific use cases.
    -   *Rule:* **Never** call an Action from inside another Action.
    -   *Rule:* Prefer the `Makeable` pattern: Call `MyAction::run(...args)` instead of instantiating manually.
    -   *Rule:* Actions can call Services, but not other Actions.
    -   *Suffix requirement:* Must end with `Action` (auto-enforced by the package).

-   **Services (`App\Services`)**: Reusable business logic shared across the application.
    -   *Rule:* Services **can** call other Services.
    -   *Rule:* Use `ServiceResult` for standardized returns: `return ServiceResult::success($data)` or `return ServiceResult::failure('message')`.
    -   *Rule:* If `Makeable` is used with a custom method, ensure `protected string $makeableMethod = 'methodName'` is declared.
    -   *Suffix requirement:* Must end with `Service` (auto-enforced).

-   **DTOs - Data Transfer Objects (`App\DTOs`)**: Read-only data containers passed between layers.
    -   *Rule:* DTOs are `final readonly` by default. Only disable with explicit `--no-final` or `--no-readonly` flags.
    -   *Rule:* Attach `InteractsWithDTO` trait to unlock `MyDTO::fromRequest($request)` and `MyDTO::fromArray($data)`.
    -   *Suffix requirement:* Must end with `DTO` (auto-enforced).

-   **Enums (`App\Enums`)**: Constant and status definitions using PHP native Enums.
    -   *Rule:* Default backing is `string`. Use `#[Label]`, `#[Color]`, `#[Icon]`, and `#[DefaultCase]` Attributes.
    -   *Rule:* Use `toData()` for frontend payloads. The default output is minimalist (`id`, `name` only).
    -   *Rule:* Enable additional fields explicitly: `toData(color: true, icon: true)`.
    -   *Rule:* For custom mapping use the `map` parameter: `toData(map: ['value' => 'id'])` for renaming, or `toData(map: ['extra' => fn($case) => ...])` for Closure resolution.
    -   *Rule:* Attach `InteractsWithEnum` trait for `tryFromDefault()` fallback support.
    -   *Suffix requirement:* Must end with `Enum` (auto-enforced).

-   **Cache (`App\Caches`)**: Cache layer classes.
    -   *Rule:* Use `--extend` to inherit from `CacheSupport` and get `remember()`, `forget()` and `$prefix` out of the box.
    -   *Suffix requirement:* Must end with `Cache` (auto-enforced).

-   **Helpers (`App\Helpers`)**: Utility functions.
    -   *Rule:* Use `--global` to create a `helpers.php` and auto-register it in `composer.json autoload.files`.
    -   *Suffix requirement:* Must end with `Helper` (auto-enforced).

-   **Traits / Concerns (`App\Concerns`)**: Reusable behavior blocks.
    -   *Rule:* Traits do **NOT** receive automatic suffixes. `structura:trait Loggable` → `Loggable.php`.

## 2. Naming Conventions & Suffixes

The package enforces strict naming. Always use and suggest the correct suffixes:

| Type    | Suffix    | Example              |
|---------|-----------|----------------------|
| Action  | `*Action` | `ProcessPaymentAction` |
| Service | `*Service`| `SocialAuthService`  |
| DTO     | `*DTO`    | `UserRegistrationDTO`|
| Enum    | `*Enum`   | `UserRoleEnum`       |
| Cache   | `*Cache`  | `ClassificationCache`|
| Helper  | `*Helper` | `StringHelper`       |
| Trait   | *(none)*  | `HasAvatar`          |

## 3. Configuration Awareness

Always check `config/structura.php` (if present) before generating files. It is the source of truth for:
- Namespaces and paths per class type
- Default options (e.g., `makeable: true` means all new Actions get Makeable by default)
- Custom suffixes per class type
- CLI flags always **override** config defaults

Key defaults in a standard install:
```php
'action'  => ['execute' => true, 'makeable' => true, 'transaction' => false]
'service' => ['makeable' => false, 'result' => false]
'enum'    => ['backed' => 'string']
```

## 4. Commands Integration

When generating new files, use the correct Artisan commands:

```bash
# Actions
php artisan structura:action Name              # Default: execute() + Makeable
php artisan structura:action Name --handle     # (-l) handle() method
php artisan structura:action Name --invokable  # (-i) __invoke() method
php artisan structura:action Name --transaction # (-t) DB::transaction() wrapper
php artisan structura:action Name --raw        # (-r) empty class

# Services
php artisan structura:service Name --method=process --result --makeable

# DTOs
php artisan structura:dto Name --trait   # Enables fromRequest() / fromArray()

# Enums
php artisan structura:enum Name --backed=string --cases=A,B,C --label --trait

# Cache
php artisan structura:cache Name --extend

# Helpers
php artisan structura:helper Name --global
```

*(Suffixes are automatically appended based on `config/structura.php`)*

---
**Summary for AI agents:**
Separate layers strictly. Orchestrate with `*Action::run()`, process with `*Service` returning `ServiceResult`, carry data with `*DTO`, define constants with `*Enum` using `toData()`, and cache with `*Cache`. Use the generator commands to ensure consistency. Never hardcode namespaces — always reference `config/structura.php`.
