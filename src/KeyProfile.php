<?php

namespace APHunt\EncryptedEmbeddables;

/**
 * Class KeyProfile
 *
 * A basic implementation of @see KeyProfileInterface using
 * encryption and keys provided by the @see \ParagonIE\Halite\ package
 *
 * @package APHunt\EncryptedEmbeddables
 */
class KeyProfile implements KeyProfileInterface
{
    /** @var string */
    private $name;

    /** @var \ParagonIE\Halite\Symmetric\EncryptionKey */
    private $key;

    /** @var bool */
    private $shouldBeRolled;

    public function __construct(string $name, \ParagonIE\Halite\Symmetric\EncryptionKey $key, bool $shouldBeRolled = false)
    {
        $this->name = $name;
        $this->key = $key;
        $this->shouldBeRolled = $shouldBeRolled;
    }

    /**
     * The name of the key profile
     *
     * Key profile names are stored along side the encrypted values,
     * so that the correct decryption key can be identified
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Encrypt the given plaintext using the key in the profile
     *
     * @param string $plaintext
     * @return string
     */
    public function encrypt(string $plaintext): string
    {
        return \ParagonIE\Halite\Symmetric\Crypto::encrypt(new \ParagonIE\Halite\HiddenString($plaintext), $this->key);
    }

    /**
     * Decrypt the given cipher text using the key in the profile
     *
     * @param string $cipherText
     * @return string
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     */
    public function decrypt(string $cipherText): string
    {
        return \ParagonIE\Halite\Symmetric\Crypto::decrypt($cipherText, $this->key);
    }

    /**
     * Determines whether values encrypted with this key should
     * be re-encrypted with the default key when they are updated.
     *
     * @return bool
     */
    public function shouldBeRolled(): bool
    {
        return $this->shouldBeRolled;
    }
}