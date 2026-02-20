# medas-php-class-analysis

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

A PHP library for statically analysing PHP class files. Given a class name, a `ReflectionClass`, or raw PHP source code, it extracts:

- **Namespace** and **fully qualified class name (FQN)**
- **Class type** — class, interface, trait, or enum
- **Modifiers** — `abstract`, `final`
- **Import statements** (`use …`) with their aliases
- **Inheritance** — `extends` and `implements` references
- **All type references** used throughout the file — in docblocks, property types, parameter/return types, `new` expressions, `instanceof` checks, attribute declarations, `ClassName::…` usages, and trait `use` statements

## Requirements

- PHP 8.4+
- [`morphp/medas-core`](https://github.com/tarantuli/medas-core) ^3
- [`morphp/medas-php-tokenizer`](https://github.com/tarantuli/medas-php-tokenizer) ^3

## Installation

```bash
composer require morphp/medas-php-class-analysis
```

## Usage

### Via the service container (recommended)

Register the package in your Medas service manager bootstrap:

```php
use Medas\PhpClassAnalysis\PhpClassAnalysisPackage;
use Medas\ServiceManager\ServiceManager;

ServiceManager::instance()->loadPackage(PhpClassAnalysisPackage::instance());
```

Then resolve `ClassAnalyser` through the container:

```php
use Medas\PhpClassAnalysis\ClassAnalyser;

$analyser = service(ClassAnalyser::class);
```

### Analysing a class by name

```php
$analysis = $analyser->analyseClassByName(MyClass::class);
```

### Analysing via a `ReflectionClass`

```php
$reflection = new ReflectionClass(MyClass::class);
$analysis   = $analyser->analyseClass($reflection);
```

### Analysing raw PHP source code

```php
$source   = file_get_contents('/path/to/MyClass.php');
$analysis = $analyser->analyse($source);
```

### Working with the result

All results are returned as a `ClassAnalysis` object:

```php
$analysis->namespace;      // e.g. 'App\Services'
$analysis->name;           // e.g. 'MyService'
$analysis->fqn;            // e.g. '\App\Services\MyService'

$analysis->isClass;        // bool
$analysis->isInterface;    // bool
$analysis->isTrait;        // bool
$analysis->isEnum;         // bool

$analysis->isAbstract;     // bool
$analysis->isFinal;        // bool

// ClassReference[] — keyed by its short name / alias
$analysis->imports;        // use statements
$analysis->extends;        // extended classes/interfaces
$analysis->implements;     // implemented interfaces
$analysis->uses;           // all referenced class names throughout the file

// Resolve an import alias to its FQN
$fqn = $analysis->resolveImport('MyAlias'); // '\Full\Qualified\Name' or null
```

Each `ClassReference` has two properties:

```php
$reference->label;  // the short name or alias used in source code
$reference->fqn;    // the resolved fully qualified name (with leading \)
```

### Generic type extension (docblock `@extends`)

For collection-style classes annotated with `@extends SomeCollection<ItemType>`, the analyser
populates `$analysis->extensionType` with a `ClassReference` for the generic type parameter:

```php
/** @extends LazyGenericCollection<Book> */
class Books extends LazyGenericCollection {}

$analysis->extensionType->label; // 'Book'
$analysis->extensionType->fqn;   // '\Fully\Qualified\Book'
```

## Running tests

```bash
composer install
./vendor/bin/phpunit
```

## Architecture

| Class                     | Responsibility                                              |
|---------------------------|-------------------------------------------------------------|
| `ClassAnalyser`           | Entry point; orchestrates the three finders                 |
| `ClassAnalysis`           | Value object holding all analysis results                   |
| `ClassReference`          | Immutable pair of short label + resolved FQN                |
| `ImportsFinder`           | Extracts `use …` import statements                          |
| `NameFinder`              | Extracts namespace, class name, type, and modifiers         |
| `ReferenceFinder`         | Extracts all class references (types, docblocks, new, etc.) |
| `FqnProperties`           | Utility for splitting/inspecting FQN strings                |
| `PhpKeywords`             | Constants listing PHP reserved words and built-in types     |
| `PhpClassAnalysisPackage` | Medas service-manager package registration                  |
