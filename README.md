<h1 align="center">Laravel-Structura</h1>

## 🌟 Introduction

<p align="justify">
<strong>Laravel-Structura</strong> is a Laravel package designed to streamline and standardize the creation of application resources, promoting a clean, scalable, and well-structured development environment.
<br>
By providing custom Artisan commands, it enables the automatic generation of <code>Action</code>, <code>Cache</code>, and <code>Service</code> classes-encouraging separation of concerns and adherence to architectural best practices.
<br>
Its main goal is to simplify development workflows by reducing repetitive tasks and ensuring consistency across your Laravel projects.
<p/>

## ✨ Features

- ✅ Generate **Action** classes
- ✅ Generate **Cache** classes
- ✅ Generate **Service** classes
- ✅ Automatically organize namespaces
- ✅ Enforce naming conventions for clarity and maintainability

## 🛠 Requirements

- PHP **^8.2**
- Laravel **^10.0**

## 📦 Installation

```bash
composer require kaue-f/laravel-structura
```

## 📚 Usage Examples

### Action

```bash
php artisan make:action Logout 
php artisan make:action Logout --execute    #Default
php artisan make:action Logout --invoke
php artisan make:action Logout --raw
```

### Cache

```bash
php artisan make:cache Classification 
php artisan make:cache Classification --base    #Default
php artisan make:cache Classification --raw
```

### Service

```bash
php artisan make:service Comment
php artisan make:service Comment --construct    #Default
php artisan make:service Comment --raw
```

## 🧱 Example Structure

```
app/
├── Actions/
│   └── LogoutAction.php
│
├── Services/
│   └──Caches/
│       ├── BaseCache.php
│       └── ClassificationCache.php 
│   └── CommentService.php
```

## 📄 License

Released under the [MIT License](LICENSE.md).
