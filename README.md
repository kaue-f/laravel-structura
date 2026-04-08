<p align="right">
  English | <a href="./docs/README_pt-BR.md">🇧🇷 Português</a>
</p>

<h1 align="center">Laravel Structura</h1>

>**Structura** comes from Latin and means structure and organization, reflecting the package’s purpose.

## 🌟 Introduction

**Laravel Structura** is a Laravel package designed to simplify, standardize, and structure the creation of application resources, promoting a clean, scalable, and well-organized development environment.

Through custom Artisan commands, the package enables the automatic generation of classes such as `Actions`, `Cache`, `DTOs`, `Enums`, `Helpers`, `Services` and `Traits`, encouraging clear separation of responsibilities and solid architectural best practices.

The main goal of Structura is to reduce repetitive tasks, ensure structural consistency, and help developers keep Laravel projects well-organized as they grow.

## ✨ Features

- ✅ **Action** generation
- ✅ **Cache** generation
- ✅ **DTO** generation
- ✅ **Enum** generation
- ✅ **Helper** generation
- ✅ **Trait** generation
- ✅ **Service** generation
- ✅ Automatic namespace organization
- ✅ Consistent architectural patterns
- ✅ Centralized configuration via the `config/structura.php` file
- ✅ CLI options override default configuration

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

This command creates a new `structura.php` file in the Laravel application's `config` directory.

## 📌 Available commands

| Command             | Description                                   |
| ------------------- | --------------------------------------------- |
| `structura:action`  | Create **Action** classes                     |
| `structura:cache`   | Create **Cache** classes                      |
| `structura:dto`     | Create **Data Transfer Object (DTO)** classes |
| `structura:enum`    | Create **Enum** classes based on PHP 8.1 Attributes |
| `structura:helper`  | Create **Helper** classes or global helpers   |
| `structura:service` | Create **Service** classes                    |
| `structura:trait`   | Create **Trait** classes                      |
| `structura:install` | Publish Structura configuration file          |

### 📚 Usage examples

#### Action

```bash
php artisan structura:action Logout
php artisan structura:action Logout --execute    # Default (-e)
php artisan structura:action Logout --handle     # (-l)
php artisan structura:action Logout --invokable  # (-i)
php artisan structura:action Logout --construct  # (-c)
php artisan structura:action Logout --makeable   # (-m) Attaches Makeable trait for MinhaAction::run() usage
php artisan structura:action Logout --transaction # (-t) Wraps method content inside DB::transaction()
php artisan structura:action Logout --raw        # (-r)
```

> Default method is execute().
> Use --handle, --invokable, or --construct to change it.
> The --makeable makes action executable via static pattern.
> The --transaction protects the block against database failures.

#### Cache

```bash
php artisan structura:cache Classification
php artisan structura:cache Classification --extend   # (-e)
php artisan structura:cache Classification --raw      # (-r)
```

> Use --extend to extend Cache Support
> The --raw creates a standalone Cache class.

#### DTO

```bash
php artisan structura:dto User
php artisan structura:dto User --no-final
php artisan structura:dto User --no-readonly
php artisan structura:dto User --no-construct
php artisan structura:dto User --trait        # (-t)
php artisan structura:dto User --raw          # (-r)
```

> Built strictly with PHP 8.2 readonly principles.
> Leverage `MyDTO::fromRequest($request)` or `MyDTO::fromArray($data)` features when inheriting DTOSupport.

#### Enum

```bash
php artisan structura:enum Status
php artisan structura:enum Status --backed=string
php artisan structura:enum Status --cases=ACTIVE,INACTIVE
php artisan structura:enum Status --label        # (-l) Uses modern PHP 8.1 #[Label], #[Icon], #[Color] attributes
php artisan structura:enum Status --trait        # (-t) Attaches InteractsWithEnum for tryFromDefault() fallback
```

> Creates PHP native Enums with elegant declarative metadata through Attributes, skipping old match() boilerplate.

#### Helper

```bash
php artisan structura:helper StringHelper
php artisan structura:helper StringHelper --example   # (-e)
php artisan structura:helper StringHelper --global    # (-g) Auto registers in composer.json files array
php artisan structura:helper --stub                   # (-s)
```

> The --global flag flawlessly updates composer block and calls dump-autoload without leaving the terminal!

#### Service

```bash
php artisan structura:service Comment
php artisan structura:service Comment --construct   # (-c)
php artisan structura:service Comment --method=process # (-m) Specifies core method signature
php artisan structura:service Comment --result      # (--res) Automates KaueF\Structura\Support\ServiceResult return types
```

> Services encapsulate business logic. Modernize error-handling and controller boundaries by returning ServiceResult.

#### Trait

```bash
php artisan structura:trait Loggable
```

> Traits are reusable behaviors for classes.

### 🧱 Example Structure

```
app/
├── Actions/
│   └── LogoutAction.php
│
├── Cache/
│   └── ClassificationCache.php
│
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
