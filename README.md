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

Let's consider a simple User class with two properties and some setup code:

```php
final class User
{
    private $id;
    private $name;

    public function __construct(int $id, string $name) { /* ... */ }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
}

$user = new User(1, 'Thomas');

$formats = new FormatContainer();
$formats->add('json', new JsonFormat());

$hydrators = new FallbackHydratorContainer();
$normalizers = new FallbackNormalizerContainer();
$serializard = new Serializard($formats, $normalizers, $hydrators);
```

## Serialization

Serialization is controlled by registering handlers used in normalization phase:

```php
$normalizers->add(User::class, function(User $user) {
    return [
        'id' => $user->getId(),
        'name' => $user->getName(),
    ];
});

$result = $serializard->serialize($user, 'json');
// result is {"id":1,"name":"Thomas"}
```

## Unserialization

Unserialization can be controlled by registering callables able to reconstruct objects from data parsed from input text:

```php
$hydrators->add(User::class, function(array $data) {
    return new User($data['id'], $data['name']);
});

$json = '{"id":1,"name":"Thomas"}';
$user = $serializard->unserialize($json, User::class, 'json');
```

# Formats

- **JSON** in `JsonFormat` converts objects to JSON,
- **Array** in `ArrayFormat` just returns object graph normalized to arrays of scalars,
- **YAML** in `YamlFormat` converts objects to YAML (uses `symfony/yaml`),
- **XML** in `XmlFormat` converts objects to XML (uses `ext-dom`).

# License

See LICENSE file in the main directory of this library.
