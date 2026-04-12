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

- ✅ Geração de **Action** com suporte a Makeable e Transaction
- ✅ Geração de **Cache** com extensão CacheSupport
- ✅ Geração de **DTO** com padrões readonly/final
- ✅ Geração de **Enum** com PHP Attributes e mapeamento `toData()`
- ✅ Geração de **Helper** com registro automático no autoload global
- ✅ Geração de **Trait**
- ✅ Geração de **Service** com ServiceResult e suporte a Makeable
- ✅ Organização automática de namespaces
- ✅ Padrões arquiteturais consistentes
- ✅ Configuração centralizada via o arquivo `config/structura.php`
- ✅ Opções da CLI sobrescrevem a configuração padrão
- ✅ Sufixos automáticos por tipo de classe

## 🛠 Requisitos

- PHP **^8.2**
- Laravel **^10.x | ^11.x | ^12.x**

## 📦 Instalação

```bash
composer require kaue-f/laravel-structura --dev
```

### ⚙️ Publicação do arquivo de configuração

```bash
php artisan structura:install
php artisan structura:install --force   # Força a sobrescrita
```

Este comando cria um novo arquivo `structura.php` no diretório `config` da aplicação Laravel. Ele controla namespaces, caminhos, sufixos e opções padrão para cada gerador.

### 🚀 Integração com IA (Laravel Boost)

Se você utiliza o [Laravel Boost](https://github.com/kaue-f/laravel-boost) ou ferramentas similares de assistência por IA, você pode adicionar a Skill do Structura automaticamente para que a IA entenda e siga os padrões arquiteturais do pacote corretamente:

```bash
php artisan boost:add-skill kaue-f/laravel-structura
```

## 📌 Comandos disponíveis

| Comando             | Descrição                                               |
| ------------------- | ------------------------------------------------------- |
| `structura:action`  | Criar classes de **Action** com suporte a Makeable e Transaction |
| `structura:cache`   | Criar classes de **Cache** com extensão CacheSupport    |
| `structura:dto`     | Criar classes de **Data Transfer Object (DTO)**         |
| `structura:enum`    | Criar classes **Enum** com mapeamento de PHP Attributes |
| `structura:helper`  | Criar classes **Helper** ou helpers globais             |
| `structura:service` | Criar classes de **Service** com Result e Makeable      |
| `structura:trait`   | Criar classes de **Trait**                              |
| `structura:install` | Publicar o arquivo de configuração do Structura         |

### 📚 Exemplos de uso

#### Action

```bash
php artisan structura:action Logout
php artisan structura:action Logout --execute     # Padrão (-e): gera o método execute()
php artisan structura:action Logout --handle      # (-l): gera o método handle()
php artisan structura:action Logout --invokable   # (-i): gera o método __invoke()
php artisan structura:action Logout --construct   # (-c): gera o método __construct()
php artisan structura:action Logout --makeable    # (-m): adiciona a trait Makeable → LogoutAction::run()
php artisan structura:action Logout --transaction # (-t): envolve o método em DB::transaction()
php artisan structura:action Logout --raw         # (-r): cria classe vazia sem métodos
```

> **Método padrão:** `execute()`. Substitua com `--handle`, `--invokable` ou `--construct`.
>
> **Dica Pro:** Por padrão, o `makeable` é `true` no `config/structura.php` — toda nova Action já nasce pronta para ser chamada com `LogoutAction::run()`!

#### Cache

```bash
php artisan structura:cache Classification
php artisan structura:cache Classification --extend  # (-e): estende CacheSupport e adiciona a propriedade $prefix
php artisan structura:cache Classification --raw     # (-r): classe independente sem CacheSupport
```

> Use `--extend` para herdar métodos auxiliares do `CacheSupport` (e.g., `remember()`, `forget()`).
> `--raw` cria uma classe simples. `--extend` e `--raw` são mutuamente exclusivos.

#### DTO

```bash
php artisan structura:dto User
php artisan structura:dto User --no-final         # Remove o modificador final
php artisan structura:dto User --no-readonly      # Remove o modificador readonly
php artisan structura:dto User --no-construct     # Remove o método __construct
php artisan structura:dto User --trait            # (-t): adiciona a trait InteractsWithDTO
php artisan structura:dto User --raw              # (-r): classe simples sem modificadores ou helpers
```

> DTOs são `final readonly` por padrão, seguindo as boas práticas do PHP 8.2.
> Use `--trait` para desbloquear os helpers `MyDTO::fromRequest($request)` e `MyDTO::fromArray($data)`.
> `--raw` não pode ser combinado com outras flags.

#### Enum

```bash
php artisan structura:enum Status
php artisan structura:enum Status --backed=string                          # Enum tipado (string|int)
php artisan structura:enum Status --backed=string --cases=ACTIVE,INACTIVE  # Gera os cases automaticamente
php artisan structura:enum Status --label                                  # (-l): adiciona o Attribute #[Label] em cada case
php artisan structura:enum Status --trait                                  # (-t): adiciona a trait InteractsWithEnum
```

> **Uso Moderno de Enums:** Utilize o método `toData()` para integração poderosa com o frontend:
>
> ```php
> Status::toData();                                               // Minimalista: ['id' => '...', 'name' => '...']
> Status::toData(color: true, icon: true);                        // Inclui cor e ícone
> Status::toData(map: ['value' => 'id', 'label' => 'name']);     // Renomeia chaves customizadas
> Status::toData(map: ['extra' => fn($case) => $case->extra()]); // Resolução via Closure
> ```
>
> A trait `InteractsWithEnum` adiciona suporte ao fallback `tryFromDefault()`. Os Attributes `#[Label]`, `#[Color]`, `#[Icon]` e `#[DefaultCase]` são suportados.

#### Helper

```bash
php artisan structura:helper StringHelper
php artisan structura:helper StringHelper --example  # (-e): gera um método de exemplo
php artisan structura:helper StringHelper --global   # (-g): cria helpers.php global e registra automaticamente no composer.json + roda dump-autoload
php artisan structura:helper --stub                  # (-s): cria helper a partir do stub do pacote
```

> A flag `--global` atualiza automaticamente o `composer.json` e roda `composer dump-autoload` para que suas funções globais fiquem disponíveis imediatamente.

#### Service

```bash
php artisan structura:service Comment
php artisan structura:service Comment --construct              # (-c): adiciona __construct()
php artisan structura:service Comment --method=process         # (--m): gera um método com nome customizado
php artisan structura:service Comment --result                 # (--res): o método retorna ServiceResult
php artisan structura:service Comment --makeable               # (--mk): adiciona a trait Makeable
```

> **ServiceResult** padroniza as respostas do seu service:
> ```php
> return ServiceResult::success($data);
> return ServiceResult::failure('Mensagem de erro');
> ```
>
> **Geração Inteligente:** Combinar `--method=process` com `--makeable` injeta automaticamente `protected string $makeableMethod = 'process'`, fazendo o `CommentService::run()` despachar para o método correto instantaneamente.

#### Trait

```bash
php artisan structura:trait Loggable
```

> Traits **não** recebem sufixo automático (diferente de Actions, Services, etc.). Então `structura:trait Loggable` gera `Loggable.php`, não `LoggableTrait.php`.

### ⚙️ Configuração Padrão (`config/structura.php`)

Após publicar, você pode definir padrões globais no `config/structura.php`:

```php
'default_options' => [
    'action' => [
        'execute'     => true,   // Método padrão gerado
        'makeable'    => true,   // Todas as novas Actions recebem a trait Makeable
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
        'backed' => 'string', // Tipo de backing padrão: 'string' | 'int' | null
    ],
    // ...
],
```

> As flags da CLI sempre sobrescrevem os padrões do config.

### 🧱 Estrutura de exemplo

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

## 📄 Licença

Distribuído sob a [Licença MIT](LICENSE.md).
