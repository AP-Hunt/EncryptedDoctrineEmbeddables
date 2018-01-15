<?php

namespace APHunt\EncryptedEmbeddables\Traits;

use APHunt\EncryptedEmbeddables\AbstractEncryptedValue;
use \Doctrine\ORM\Mapping as ORM;

/**
 * Trait HasEncryptedFields
 *
 * Use this trait within your entities to implement the necessary lifecycle
 * callbacks for triggering the sealing and unsealing of any properties
 * identifies by the `getEncryptedFieldNames` method.
 *
 * @package APHunt\EncryptedEmbeddables\Traits;
 * @ORM\HasLifecycleCallbacks()
 */
trait HasEncryptedFields
{
    /**
     * Returns an array of property names which hold instances of AbstractEncryptedValue.
     *
     * This is used to inform which properties will be sealed/unsealed
     *
     * @return array
     */
    protected abstract function getEncryptedFieldNames() : array;

    /**
     * Called prior to a Doctrine ORM flush event. Seals any encrypted properties
     * before they're persisted.
     *
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        foreach($this->getEncryptedFieldNames() as $field)
        {
            if(property_exists($this, $field) && $this->{$field} instanceof AbstractEncryptedValue)
            {
                /** @var AbstractEncryptedValue $value */
                $value = $this->{$field};
                $value->seal();
            }
        }
    }

    /**
     * Called after Doctrine loads an entity. Unseals any encrypted properties
     * so their plaintext values can be read.
     *
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        foreach($this->getEncryptedFieldNames() as $field)
        {
            if(property_exists($this, $field) && $this->{$field} instanceof AbstractEncryptedValue)
            {
                /** @var AbstractEncryptedValue $value */
                $value = $this->{$field};
                $value->unseal();
            }
        }
    }
}