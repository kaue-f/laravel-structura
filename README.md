<p align="right">
  English | <a href="./docs/README_pt-BR.md">🇧🇷 Português</a>
</p>

<h1 align="center">Laravel Structura</h1>

> **Structura** comes from Latin and means structure and organization, reflecting the package's purpose.

## 🌟 Introduction

**Laravel Structura** is a Laravel package designed to simplify, standardize, and structure the creation of application resources, promoting a clean, scalable, and well-organized development environment.

Through custom Artisan commands, the package enables the automatic generation of classes such as `Actions`, `Cache`, `DTOs`, `Enums`, `Helpers`, `Services` and `Traits`, encouraging clear separation of responsibilities and solid architectural best practices.

The main goal of Structura is to reduce repetitive tasks, ensure structural consistency, and help developers keep Laravel projects well-organized as they grow.

## ✨ Features

- ✅ **Action** generation with Makeable & Transaction support
- ✅ **Cache** generation with CacheSupport extension
- ✅ **DTO** generation with readonly/final patterns
- ✅ **Enum** generation with PHP Attributes and `toData()` mapping
- ✅ **Helper** generation with global autoload registration
- ✅ **Trait** generation
- ✅ **Service** generation with ServiceResult and Makeable support
- ✅ Automatic namespace organization
- ✅ Consistent architectural patterns
- ✅ Centralized configuration via the `config/structura.php` file
- ✅ CLI options override default configuration
- ✅ Automatic suffix enforcement per class type

## 🛠 Requirements

- PHP **^8.2**
- Laravel **^10.x | ^11.x | ^12.x**

## 📦 Installation

```bash
composer require kaue-f/laravel-structura --dev
```

### ⚙️ Publishing the configuration file

```bash
php artisan structura:install
php artisan structura:install --force   # Force overwrite
```

This command creates a new `structura.php` file in the Laravel application's `config` directory. It controls namespaces, paths, suffixes, and default options for each generator.

### 🚀 AI Integration (Laravel Boost)

This package ships with a built-in [Laravel Boost](https://laravel.com/docs/boost) skill. If your project uses Boost, the Structura skill is automatically discovered and installed when you run:

```bash
php artisan boost:install
```

Once installed, your AI agent will understand the Structura architecture — Actions, Services, DTOs, Enums, Caches, and Helpers — and follow all naming conventions and patterns automatically.

## 📌 Available commands

| Command             | Description                                          |
| ------------------- | ---------------------------------------------------- |
| `structura:action`  | Create **Action** classes with Makeable & Transaction support |
| `structura:cache`   | Create **Cache** classes with optional CacheSupport  |
| `structura:dto`     | Create **Data Transfer Object (DTO)** classes        |
| `structura:enum`    | Create **Enum** classes with PHP Attribute mapping   |
| `structura:helper`  | Create **Helper** classes or global helpers          |
| `structura:service` | Create **Service** classes with Result & Makeable    |
| `structura:trait`   | Create **Trait** classes                             |
| `structura:install` | Publish Structura configuration file                 |

### 📚 Usage examples

#### Action

```bash
php artisan structura:action Logout
php artisan structura:action Logout --execute     # Default (-e): generates execute() method
php artisan structura:action Logout --handle      # (-l): generates handle() method
php artisan structura:action Logout --invokable   # (-i): generates __invoke() method
php artisan structura:action Logout --construct   # (-c): generates __construct() method
php artisan structura:action Logout --makeable    # (-m): attaches Makeable trait → LogoutAction::run()
php artisan structura:action Logout --transaction # (-t): wraps method body in DB::transaction()
php artisan structura:action Logout --raw         # (-r): creates empty class body
```

> **Default method:** `execute()`. Override with `--handle`, `--invokable`, or `--construct`.
>
> **Pro Tip:** By default, `makeable` is `true` in `config/structura.php` — every new Action is ready for `LogoutAction::run()` usage out of the box!

#### Cache

```bash
php artisan structura:cache Classification
php artisan structura:cache Classification --extend  # (-e): extends CacheSupport, adds $prefix property
php artisan structura:cache Classification --raw     # (-r): standalone class without CacheSupport
```

> Use `--extend` to inherit helper methods from `CacheSupport` (e.g., `remember()`, `forget()`).
> `--raw` creates a plain class. `--extend` and `--raw` are mutually exclusive.

#### DTO

```bash
php artisan structura:dto User
php artisan structura:dto User --no-final         # Removes the final modifier
php artisan structura:dto User --no-readonly      # Removes the readonly modifier
php artisan structura:dto User --no-construct     # Removes the __construct method
php artisan structura:dto User --trait            # (-t): attaches InteractsWithDTO trait
php artisan structura:dto User --raw              # (-r): plain class, no modifiers or helpers
```

> DTOs are `final readonly` by default following PHP 8.2 best practices.
> Attach `--trait` to unlock `MyDTO::fromRequest($request)` and `MyDTO::fromArray($data)` helpers.
> `--raw` cannot be combined with other flags.

#### Enum

```bash
php artisan structura:enum Status
php artisan structura:enum Status --backed=string              # Backed enum (string|int)
php artisan structura:enum Status --backed=string --cases=ACTIVE,INACTIVE  # Pre-generates cases
php artisan structura:enum Status --label                      # (-l): adds #[Label] attribute to each case
php artisan structura:enum Status --trait                      # (-t): attaches InteractsWithEnum trait
```

> **Modern Enum Usage:** Use `toData()` for powerful frontend integration:
>
> ```php
> Status::toData();                                              // Minimalist: ['id' => '...', 'name' => '...']
> Status::toData(color: true, icon: true);                       // Includes color and icon attributes
> Status::toData(map: ['value' => 'id', 'label' => 'name']);    // Custom key renaming
> Status::toData(map: ['extra' => fn($case) => $case->extra()]); // Closure resolution
> ```
>
> The `InteractsWithEnum` trait adds `tryFromDefault()` fallback support. `#[Label]`, `#[Color]`, `#[Icon]`, and `#[DefaultCase]` Attributes are supported.

#### Helper

```bash
php artisan structura:helper StringHelper
php artisan structura:helper StringHelper --example  # (-e): generates example method
php artisan structura:helper StringHelper --global   # (-g): creates global helpers.php and auto-registers it in composer.json autoload.files + runs dump-autoload
php artisan structura:helper --stub                  # (-s): creates helper from the package's own stub
```

> The `--global` flag automatically updates `composer.json` and runs `composer dump-autoload` so your global functions are immediately available.

#### Service

```bash
php artisan structura:service Comment
php artisan structura:service Comment --construct              # (-c): adds __construct() method
php artisan structura:service Comment --method=process         # (--m): generates a custom named method
php artisan structura:service Comment --result                 # (--res): method returns ServiceResult
php artisan structura:service Comment --makeable               # (--mk): attaches Makeable trait
```

> **ServiceResult** standardizes your service responses:
> ```php
> return ServiceResult::success($data);
> return ServiceResult::failure('Error message');
> ```
>
> **Smart Generation:** Combining `--method=process` with `--makeable` automatically injects `protected string $makeableMethod = 'process'` so `CommentService::run()` dispatches to the right method instantly.

#### Trait

```bash
php artisan structura:trait Loggable
```

> Traits do **not** receive automatic suffixes (unlike Actions, Services, etc.). So `structura:trait Loggable` generates `Loggable.php`, not `LoggableTrait.php`.

### ⚙️ Default Configuration (`config/structura.php`)

After publishing, you can set package-wide defaults in `config/structura.php`:

```php
'default_options' => [
    'action' => [
        'execute'     => true,   // Default method
        'makeable'    => true,   // All new actions get Makeable trait by default
        'transaction' => false,
    ],
    'service' => [
        'makeable' => false,
        'result'   => false,
    ],
    'dto' => [
        'no-final'    => false,
        'no-readonly' => false,
    ],
    'enum' => [
        'backed' => 'string', // Default backing type: 'string' | 'int' | null
    ],
    // ...
],
```

> CLI flags always override config defaults.

### 🧱 Example Structure

```
app/
├── Actions/
│   └── LogoutAction.php
│
├── Caches/
│   └── ClassificationCache.php
│
├── Concerns/
│   └── Loggable.php
│
├── DTOs/
│   └── UserDTO.php
│
├── Enums/
│   └── StatusEnum.php
│
├── Helpers/
│   ├── helpers.php
│   ├── StringHelper.php
│   └── string_helper.php
│
├── Services/
│   └── CommentService.php
```

## 📄 License

Released under the [MIT License](LICENSE.md).
