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
composer require kaue-f/laravel-structura --dev
```

### ⚙️ Publicação do arquivo de configuração

```bash
php artisan structura:install
php artisan structura:install --force   # Força a sobrescrita
```

Este comando cria um novo arquivo structura.php no diretório config da aplicação Laravel.

### 🚀 Integração com IA (Laravel Boost)

Se você utiliza o [Laravel Boost](https://github.com/kaue-f/laravel-boost) ou ferramentas similares de assistência por IA, você pode adicionar a Skill do Structura automaticamente para que a IA entenda e siga os padrões arquiteturais do pacote corretamente:

```bash
php artisan boost:add-skill kaue-f/laravel-structura
```

## 📌 Comandos disponíveis

| Comando             | Descrição                                       |
| ------------------- | ----------------------------------------------- |
| `structura:action`  | Criar classes de **Action** com suporte Transactional |
| `structura:cache`   | Criar classes de **Cache**                      |
| `structura:dto`     | Criar classes de **Data Transfer Object (DTO)** |
| `structura:enum`    | Criar classes **Enum** com mapeamento de Attributes |
| `structura:helper`  | Criar classes **Helper** ou helpers globais     |
| `structura:service` | Criar classes de **Service** com automação de Result |
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
php artisan structura:action Logout --makeable   # (-m) Adiciona suporte a chamada estática direta
php artisan structura:action Logout --transaction # (-t) Protege o escopo com DB::transaction()
php artisan structura:action Logout --raw        # (-r) Cria classe vazia
```

> **Dica Pro:** Por padrão, o `makeable` vem como `true` no arquivo `config/structura.php`, garantindo que todas as suas actions já nasçam prontas para serem chamadas com `LogoutAction::run()`!

#### Enum

```bash
php artisan structura:enum Status
php artisan structura:enum Status --backed=string
php artisan structura:enum Status --cases=ACTIVE,INACTIVE
php artisan structura:enum Status --label        # (-l) Usa modernos Atributos #[Label], #[Icon], #[Color]
php artisan structura:enum Status --trait        # (-t) Adiciona recursos nativos de fallback
```

> **Uso Moderno de Enums:** Utilize o método `toData()` para uma integração poderosa com o frontend:
> ```php
> Status::toData(); // Minimalista: retorna ['id' => '...', 'name' => '...']
> Status::toData(color: true, icon: true); // Inclui atributos de cor e ícone
> Status::toData(map: ['value' => 'id', 'label' => 'name']); // Renomeia chaves customizadas
> Status::toData(map: ['extra' => fn($case) => $case->getExtra()]); // Resolução via Closure
> ```

#### Helper

```bash
php artisan structura:helper StringHelper
php artisan structura:helper StringHelper --example   # (-e)
php artisan structura:helper StringHelper --global    # (-g) Automação de composer autoload global
php artisan structura:helper --stub                   # (-s)
```

#### Service

```bash
php artisan structura:service Comment
php artisan structura:service Comment --construct   # (-c)
php artisan structura:service Comment --method=process # (-m) Define o método principal
php artisan structura:service Comment --result      # (--res) Automatiza retornos do tipo ServiceResult
php artisan structura:service Comment --makeable    # (--mk) Permite o uso do padrão ::run()
```

> **Geração Inteligente de Services:** Se você passar as flags `--method` e `--makeable` juntas, o Structura injeta automaticamente a propriedade `$makeableMethod` para que o `CommentService::run()` funcione instantaneamente!

> Services encapsulam regras de negócio. 
> Evite códigos complexos e confie no tipo de retorno nativo integrado da própria Structura acionando o `--result`.

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
