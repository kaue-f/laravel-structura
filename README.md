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

### 🚀 AI Integration (Laravel Boost)

If you use [Laravel Boost](https://github.com/kaue-f/laravel-boost) or similar AI assistance tools, you can add the Structura Skill automatically to help the AI understand and follow the package's architectural patterns correctly:

```bash
php artisan boost:add-skill kaue-f/laravel-structura
```

## 📌 Available commands

| Command             | Description                                   |
| ------------------- | --------------------------------------------- |
| `structura:action`  | Create **Action** classes with Transactional support |
| `structura:cache`   | Create **Cache** classes                      |
| `structura:dto`     | Create **Data Transfer Object (DTO)** classes |
| `structura:enum`    | Create **Enum** classes with Attribute mapping |
| `structura:helper`  | Create **Helper** classes or global helpers   |
| `structura:service` | Create **Service** classes with Result automation |
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
php artisan structura:action Logout --makeable   # (-m) Attaches Makeable trait for Static execution
php artisan structura:action Logout --transaction # (-t) Protects scope with DB::transaction()
php artisan structura:action Logout --raw        # (-r) Creates empty class
```

> **Pro Tip:** By default, `makeable` is set to `true` in `config/structura.php`, ensuring all your actions are ready for `LogoutAction::run()` usage!

#### Enum

```bash
php artisan structura:enum Status
php artisan structura:enum Status --backed=string
php artisan structura:enum Status --cases=ACTIVE,INACTIVE
php artisan structura:enum Status --label        # (-l) Uses #[Label], #[Icon], #[Color] attributes
php artisan structura:enum Status --trait        # (-t) Attaches InteractsWithEnum fallback support
```

> **Modern Enum Usage:** Use `toData()` for powerful frontend integration:
>
> ```php
> Status::toData(); // Minimalist: returns ['id' => '...', 'name' => '...']
> Status::toData(color: true, icon: true); // Includes attributes
> Status::toData(map: ['value' => 'id', 'label' => 'name']); // Custom key renaming
> Status::toData(map: ['extra' => fn($case) => $case->getExtra()]); // Closure resolution
> ```

#### Helper

```bash
php artisan structura:helper StringHelper
php artisan structura:helper StringHelper --example   # (-e)
php artisan structura:helper StringHelper --global    # (-g) Auto-registers in composer.json
php artisan structura:helper --stub                   # (-s)
```

#### Service

```bash
php artisan structura:service Comment
php artisan structura:service Comment --construct   # (-c)
php artisan structura:service Comment --method=process # (-m) Specifies core method
php artisan structura:service Comment --result      # (--res) Automates ServiceResult return types
php artisan structura:service Comment --makeable    # (--mk) Enables ::run() usage
```

> **Smart Service Generation:** If you provide both `--method` and `--makeable`, Structura automatically injects the `$makeableMethod` property so `CommentService::run()` works instantly!

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
