<?php

namespace APHunt\EncryptedEmbeddables;

use \Doctrine\ORM\Mapping as ORM;

/**
 * Class EncryptedString
 *
 * Implements @see AbstractEncryptedValue to support encrypted string values
 *
 * @package APHunt\EncryptedEmbeddables
 * @ORM\Embeddable()
 */
class EncryptedString extends AbstractEncryptedValue
{

    /**
     * Returns the string. Nothing else needs doing to a string value
     * before encryption.
     *
     * @param $plainTextValue
     * @return mixed
     */
    protected function encodeForSealing($plainTextValue)
    {
        return $plainTextValue;
    }

    /**
     * Returns the string. Nothing else needs doing to a string
     * value after encryption.
     *
     * @param $plainText
     * @return mixed
     */
    protected function decodeForUnsealing($plainText)
    {
        return $plainText;
    }
}