<p align="right">
  <a href="../README.md">[English]</a> | 🇧🇷 Português
</p>

<h1 align="center">Laravel Structura</h1>

> **Structura** vem do latim e significa estrutura e organização, refletindo o propósito do pacote.

## 🌟 Introdução

**Laravel Structura** é um pacote para Laravel projetado para simplificar, padronizar e estruturar a criação de recursos da aplicação, promovendo um ambiente de desenvolvimento limpo, escalável e bem organizado.

Por meio de comandos Artisan personalizados, o pacote possibilita a geração automática de classes como Actions, Cache, DTOs, Enums, Helpers, Services e Traits, incentivando uma separação clara de responsabilidades e boas práticas arquiteturais.

O principal objetivo do Structura é reduzir tarefas repetitivas, garantir consistência estrutural e ajudar desenvolvedores a manter projetos Laravel bem organizados à medida que crescem.

## ✨ Funcionalidades

- ✅ Geração de **Action**
- ✅ Geração de **Cache**
- ✅ Geração de **DTO**
- ✅ Geração de **Enum**
- ✅ Geração de **Helper**
- ✅ Geração de **Trait**
- ✅ Geração de **Service**
- ✅ Organização automática de namespaces
- ✅ Padrões arquiteturais consistentes
- ✅ Configuração centralizada via o arquivo `config/structura.php`
- ✅ Opções da CLI sobrescrevem a configuração padrão

## 🛠 Requisitos

- PHP **^8.2**
- Laravel **^10.x | ^11.x | ^12.x**

## 📦 Instalação

```bash
composer require kaue-f/laravel-structura
```

### ⚙️ Publicação do arquivo de configuração

```bash
php artisan structura:install
php artisan structura:install --force   # Força a sobrescrita
```

Este comando cria um novo arquivo structura.php no diretório config da aplicação Laravel.

## 📌 Comandos disponíveis

| Comando             | Descrição                                       |
| ------------------- | ----------------------------------------------- |
| `structura:action`  | Criar classes de **Action**                     |
| `structura:cache`   | Criar classes de **Cache**                      |
| `structura:dto`     | Criar classes de **Data Transfer Object (DTO)** |
| `structura:enum`    | Criar classes **Enum** com helpers              |
| `structura:helper`  | Criar classes **Helper** ou helpers globais     |
| `structura:service` | Criar classes de **Service**                    |
| `structura:trait`   | Criar classes de **Trait**                      |
| `structura:install` | Publicar o arquivo de configuração do Structura |

### 📚 Exemplos de uso

#### Action

```bash
php artisan structura:action Logout
php artisan structura:action Logout --execute    # Padrão (-e)
php artisan structura:action Logout --handle     # (-l)
php artisan structura:action Logout --invokable  # (-i)
php artisan structura:action Logout --construct  # (-c)
php artisan structura:action Logout --raw        # (-r)
```

> O método padrão é execute().
> Use --handle, --invokable ou --construct para alterar.
> O --raw cria uma Action sem métodos.

#### Cache

```bash
php artisan structura:cache Classification
php artisan structura:cache Classification --extend   # (-e)
php artisan structura:cache Classification --raw      # (-r)
```

> Use --extend para estende Cache Support
> O --raw cria uma classe independente.

#### DTO

```bash
php artisan structura:dto User
php artisan structura:dto User --no-final
php artisan structura:dto User --no-readonly
php artisan structura:dto User --no-construct
php artisan structura:dto User --trait        # (-t)
php artisan structura:dto User --raw          # (-r)
```

> Por padrão, DTO é final readonly com __construct.
> Use flags para desativar.
> O --trait adiciona InteractsWithDTO.
> O --raw cria um DTO mínimo.

#### Enum

```bash
php artisan structura:enum Status
php artisan structura:enum Status --backed=string
php artisan structura:enum Status --cases=ACTIVE,INACTIVE
php artisan structura:enum Status --label        # (-l)
php artisan structura:enum Status --trait        # (-t)
```

> Cria Enums nativos do PHP, opcionalmente com backed, labels ou trait anexada.

#### Helper

```bash
php artisan structura:helper StringHelper
php artisan structura:helper StringHelper --example   # Padrão (-e)
php artisan structura:helper StringHelper --global    # (-g)
php artisan structura:helper --stub                   # (-s)
php artisan structura:helper StringHelper --raw       # (-r)
```

> O --example adiciona um método de exemplo ao helper (comportamento padrão).
> O --raw cria um helper independente, sem métodos.
> O --stub gera o arquivo helpers.php a partir do stub do package e não exige nome.
> Use --global para registrar helpers globais via Composer.

#### Service

```bash
php artisan structura:service Comment
php artisan structura:service Comment --construct   # Padrão (-c)
php artisan structura:service Comment --raw         # (-r)
```

> Services encapsulam regras de negócio.
> Padrão inclui __construct.
> O --raw cria classe mínima.

#### Trait

```bash
php artisan structura:trait Loggable
```

> Traits são comportamentos reutilizáveis entre classes.

### 🧱 Estrutura de exemplo

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

## 📄 Lincença

Distribuído sob a [Licença MIT](LICENSE.md).
