<?php

namespace APHunt\EncryptedEmbeddables;

/**
 * Interface KeyProfileInterface
 *
 * Represents a profile of a key used for encryption and decryption, with
 * support for key rolling.
 *
 * @package APHunt\EncryptedEmbeddables
 */
interface KeyProfileInterface
{
    /**
     * The name of the key profile
     *
     * Key profile names are stored along side the encrypted values,
     * so that the correct decryption key can be identified
     * @return string
     */
    function getName() : string;

    /**
     * Encrypt the given plaintext using the key in the profile
     *
     * @param string $plaintext
     * @return string
     */
    function encrypt(string $plaintext) : string;

    /**
     * Decrypt the given cipher text using the key in the profile
     *
     * @param string $cipherText
     * @return string
     */
    function decrypt(string $cipherText) : string;

    /**
     * Determines whether values encrypted with this key should
     * be re-encrypted with the default key when they are updated.
     *
     * @return bool
     */
    function shouldBeRolled() : bool;
}