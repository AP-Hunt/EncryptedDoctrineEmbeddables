# Encrypted Fields
This repository contains a prototype for handling encrypted fields in Doctrine, with support for key rolling.
It does this through the use of an embeddable which provides the implementation of encryption and decryption. 

Values are encrypted/decrypted as late as possible, by reacting to an entity's lifecycle callbacks.

## Requirements
* PHP >= 7.2
* Doctrine ^2.6
* Paragon\Halite ^4

## Usage
### Entities
To add an encrypted value, add and map a new property of the necessary type to your entity, use the 
`\APHunt\EncryptedEmbeddables\Traits\HasEnryptedFields` trait and implement its abstract method. The trait 
provides hooks for the necessary lifecycle callbacks, and calls the encrypted values' `seal` and `unseal`
methods as appropriate.

```$php
use \Doctrine\ORM\Mapping as @ORM;

/**
 * @ORM\Entity
 */
class Foo
{
    use \APHunt\EncryptedEmbeddables\Traits\HasEncryptedFields;

    /**
     * @ORM\Embedded(class="\APHunt\EncryptedEmbeddables\EncryptedString")
     */
    private $bar;
    
    public function __construct()
    {
        $this->bar = new \APHunt\EncryptedEmbeddables\EncryptedString();
    }
    
    /**
     * Returns an array of property names which hold instances of AbstractEncryptedValue.
     *
     * This is used to inform which properties will be sealed/unsealed
     *
     * @return array
     */
    protected abstract function getEncryptedFieldNames() : array
    {
        reutnr ["bar"];
    }
}
```

When writing getters and setters, entities can hide the fact the field is encrypted

```$php
public function getBar()
{
    return $this->bar->getValue();
}

public function setBar(string $value)
{
    $this->bar->setValue($value);
}
```

### Key profiles
The `KeyProfile` is the part of the setup which provides encryption services to entities. During
bootstrapping of the application, all necessary encryption keys should be discovered and registered.

```$php
/** @var string[] */
$keyFiles = findMyKeyFiles();
foreach($keyFiles as $file)
{
    $requiresRolling = keyRequiresRolling($file);
    $key = \ParagonIE\Halite\KeyFactory::loadEncryptionKey($file);
    $profile = new \APHunt\EncryptedFields\KeyProfile(basename($file), $key, $requiresRolling);
    
    \APHunt\EncrypedFields\KeyProfileLoader::registerKeyProfile($profile);
    
    if(isCurrentKey($file))
    {
        \APHunt\EncrypedFields\KeyProfileLoader::setDefaultKeyProfile($profile);    
    }
}
```

In the above code, `keyRequiresRolling` would determine if values encrypted with the key require
re-encrypting with the current key. `isCurerntKey` determines if the key is the current one.

When re-sealing a value, if the named key profile is marked as requiring rolling then the default
key profile will be used instead.

## Schema
The embeddable as provided contains mappings for two string fields: `cihperText` and `keyProfile`. The foremr
holds the encrypted value, the latter holds the name of the key profile used to encrypt it. Following the
standard rules for embedded value column names, the schema for the above entity may look something like

```$sql
CREATE TABLE Foo (
    bar_cipherText as TEXT,
    bar_keyProfile as VARCHAR(255)
)
```