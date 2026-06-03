# medas-php-class-analysis

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

Statically analyses PHP class files using `medas-php-tokenizer`. Given a class name, a `ReflectionClass`, or raw PHP source, it extracts the namespace, FQN, class type, modifiers, import statements, inheritance, and every type reference used throughout the file.

**What is extracted:**

- Namespace and fully qualified class name``
- Class type: `class`, `interface`, `trait`, `enum`````
- Modifiers: `abstract`, `final`
- Import statements (`use …`) with aliases
- Inheritance: `extends` and `implements`
- All type references from: property types, parameter/return types, docblocks, `new` expressions, `instanceof` checks, attribute declarations, `ClassName::…` usages, and trait `use` statements
- Generic type parameter from `@extends` docblock (`$analysis->extensionType`)

Results are returned as a `ClassAnalysis` value object. Every type reference is a `ClassReference` with a `$label` (short name or alias as it appears in the source) and a `$fqn` (resolved fully qualified name with leading `\`).

**Architecture:**

| Class             | Responsibility                                   |
|-------------------|--------------------------------------------------|
| `ClassAnalyser`   | Entry point; orchestrates the three finders      |
| `ClassAnalysis`   | Value object holding all results                 |
| `ClassReference`  | Immutable label + resolved FQN pair              |
| `ImportsFinder`   | Extracts `use …` statements                      |
| `NameFinder`      | Extracts namespace, name, type, modifiers        |
| `ReferenceFinder` | Extracts all class references across the file    |
| `FqnProperties`   | Utility for splitting and inspecting FQN strings |
| `PhpKeywords`     | PHP reserved words and built-in type constants   |

## Usage

### Package developer context

Register the package and inject `ClassAnalyser`:

```php
use Medas\PhpClassAnalysis\PhpClassAnalysisPackage;

PhpClassAnalysisPackage::instance();
```

**Analysing a class by name:**

```php
use Medas\PhpClassAnalysis\ClassAnalyser;
use Medas\Core\Attributes\Service;

#[Service]
readonly class CodeInspector
{
    public function __construct(
        private ClassAnalyser $analyser,
    ) {}

    public function inspect(string $className): void
    {
        $analysis = $this->analyser->analyseClassByName($className);

        echo $analysis->fqn;          // e.g. '\App\Services\InvoiceService'
        echo $analysis->namespace;    // 'App\Services'
        echo $analysis->name;         // 'InvoiceService'

        echo ($analysis->isClass     ? 'class'     : '');
        echo ($analysis->isInterface ? 'interface' : '');
        echo ($analysis->isTrait     ? 'trait'     : '');
        echo ($analysis->isEnum      ? 'enum'      : '');

        echo ($analysis->isAbstract ? 'abstract ' : '');
        echo ($analysis->isFinal    ? 'final '    : '');
    }
}
```

**Analysing via `ReflectionClass`:**

```php
$reflection = new \ReflectionClass(MyClass::class);
$analysis   = $this->analyser->analyseClass($reflection);
```

**Analysing raw PHP source:**

```php
$source   = file_get_contents('/path/to/MyClass.php');
$analysis = $this->analyser->analyse($source);
```

**Working with imports:**

```php
$analysis = $this->analyser->analyseClassByName(SomeClass::class);

// All use statements: ClassReference[] keyed by short name / alias
foreach ($analysis->imports as $label => $ref) {
    echo "$label => {$ref->fqn}\n";
}

```

**Inspecting inheritance and type references:**

```php
// extends: ClassReference[]
foreach ($analysis->extends as $ref) {
    echo "Extends: {$ref->fqn}\n";
}

// implements: ClassReference[]
foreach ($analysis->implements as $ref) {
    echo "Implements: {$ref->fqn}\n";
}

// uses: all class references found anywhere in the file
foreach ($analysis->uses as $ref) {
    echo "References: {$ref->fqn}\n";
}
```

**Generic type extension (`@extends` docblock):**

```php
/**
 * @extends RecordCollection<Invoice>
 */
class InvoiceCollection extends RecordCollection {}
```

```php
$analysis = $this->analyser->analyseClassByName(InvoiceCollection::class);

$analysis->extensionType?->label; // 'Invoice'
$analysis->extensionType?->fqn;   // '\App\Entities\Invoice'
```

This is how `medas-object-to-array-serializer` resolves template type parameters when unserializing typed collections.

**Using `FqnProperties` for FQN manipulation:**

`FqnProperties` is a service — inject it rather than instantiate it directly. The FQN string is passed as a method argument.

```php
use Medas\PhpClassAnalysis\FqnProperties;
use Medas\Core\Attributes\Service;

#[Service]
readonly class MyClass
{
    public function __construct(
        private FqnProperties $fqnProperties,
    ) {}

    public function example(): void
    {
        $fqn = 'App\Services\InvoiceService'; // no leading backslash

        echo $this->fqnProperties->getFirstPart($fqn);       // 'App'
        echo $this->fqnProperties->getNextToLastPart($fqn);  // 'Services'
        echo $this->fqnProperties->getLastPart($fqn);        // 'InvoiceService'
    }
}
```

Note: `getFirstPart()` returns an empty string when the FQN has a leading `\` (e.g. `'\App\Services\InvoiceService'`). Strip the leading backslash first if needed.

### Backend user context

This package has no CLI commands and no configuration options. It is consumed as a library by other framework packages (`medas-object-to-array-serializer`, `medas-dependency-checker`, `medas-php-formatter`) and can be injected into any service that needs static PHP class analysis.

**`ImportLabelCaseMismatch`** is thrown when an import alias has a case mismatch with how it is referenced in the source. This typically indicates a typo in user-written code rather than a framework bug.

**Performance** — each `analyseClassByName()` call reads the class source file from disk and tokenizes it. For batch processing many classes, cache the `ClassAnalysis` results externally rather than re-analysing the same class repeatedly.
