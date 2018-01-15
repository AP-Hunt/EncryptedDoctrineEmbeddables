<?php

namespace APHunt\EncryptedEmbeddables;

use \Doctrine\ORM\Mapping as ORM;

/**
 * Class EncryptedArray
 *
 * Implements @see AbstractEncryptedValue to provide support for arrays
 *
 * @package APHunt\EncryptedEmbeddables
 * @ORM\Embeddable()
 */
class EncryptedArray extends AbstractEncryptedValue
{
    /**
     * @return null|array
     * @throws \App\KeyProfileNotFoundException
     */
    public function getValue()
    {
        // Overridden to change the phpdoc return signature
        return parent::getValue();
    }

    /**
     * Serializes the array prior to sealing
     *
     * @param $plainTextValue
     * @return mixed|string
     */
    protected function encodeForSealing($plainTextValue)
    {
        return serialize($plainTextValue);
    }

    /**
     * Unserializes the encrypted value to get the array value back
     *
     * @param $plainText
     * @return mixed
     */
    protected function decodeForUnsealing($plainText)
    {
        return unserialize($plainText);
    }
}