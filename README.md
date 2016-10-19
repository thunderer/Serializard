# Serializard

[![Build Status](https://travis-ci.org/thunderer/Serializard.svg?branch=master)](https://travis-ci.org/thunderer/Serializard)
[![Latest Stable Version](https://poser.pugx.org/thunderer/serializard/v/stable)](https://packagist.org/packages/thunderer/serializard)
[![License](https://poser.pugx.org/thunderer/serializard/license)](https://packagist.org/packages/thunderer/serializard)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thunderer/Serializard/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thunderer/Serializard/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/thunderer/Serializard/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/thunderer/Serializard/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/56bb2af62a29ed0034380546/badge.svg)](https://www.versioneye.com/user/projects/56bb2af62a29ed0034380546)

Serializard is a library for (un)serialization of data of any complexity. Its main focus is to give user as much flexibility as possible by delegating the (un)serialization logic to the programmer to encourage good object design and only supervising the process hiding the unpleasant details about it.

# Installation

This library is available on Composer/Packagist as `thunderer/serializard`.

# Usage

Examples in this section use this class:

```php
class User
{
    private $id;
    private $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
}
```

## Serialization

Serialization is controlled by registering callables used in normalization phase:

```php
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\FormatContainer\FormatContainer;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;
use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;
use Thunder\Serializard\Serializard;

$formats = new FormatContainer();
$formats->add('json', new JsonFormat());

$normalizers = new FallbackNormalizerContainer();
$normalizers->add(User::class, 'user', function(User $user) {
    return [
        'id' => $user->getId(),
        'name' => $user->getName(),
    ];
});

$hydrators = new FallbackHydratorContainer();

$serializard = new Serializard($formats, $normalizers, $hydrators);
echo $serializard->serialize(new User(1, 'Thomas'), 'json');
```

The result is:

```
{"id":1,"name":"Thomas"}
```

## Unserialization

Unserialization can be controlled by registering callables able to reconstruct objects from data parsed from input text:

```php
use Thunder\Serializard\Format\JsonFormat;
use Thunder\Serializard\FormatContainer\FormatContainer;
use Thunder\Serializard\HydratorContainer\FallbackHydratorContainer;
use Thunder\Serializard\NormalizerContainer\FallbackNormalizerContainer;
use Thunder\Serializard\Serializard;

$formats = new FormatContainer();
$formats->add('json', new JsonFormat());

$normalizers = new FallbackNormalizerContainer();

$hydrators = new FallbackHydratorContainer();
$hydrators->add(User::class, 'user', function(array $data) {
    return new User($data['id'], $data['name']);
});

$serializard = new Serializard($formats, $normalizers, $hydrators);
$json = '{"id":1,"name":"Thomas"}';
var_dump($serializard->unserialize($json, User::class, 'json'));
```

The result is:

```
class User#9 (2) {
  private $id =>
  int(1)
  private $name =>
  string(6) "Thomas"
}
```

# Formats

Several formats are supported as classes in `Thunder\Serializard\Format`:

- **JSON** in `JsonFormat` converts objects to JSON,
- **Array** in `ArrayFormat` just returns object graph normalized to arrays of scalars,
- **YAML** in `YamlFormat` converts objects to YAML,
- **XML** in `XmlFormat` converts objects to XML.

# License

See LICENSE file in the main directory of this library.
