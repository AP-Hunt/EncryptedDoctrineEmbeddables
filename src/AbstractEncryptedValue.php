<?php

namespace APHunt\EncryptedEmbeddables;

use App\KeyProfileNotFoundException;
use \Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEncryptedValue
 *
 * Provides the core behaviour for sealing/unsealing the value.
 *
 * @package APHunt\EncryptedEmbeddables
 * @ORM\MappedSuperclass()
 */
abstract class AbstractEncryptedValue
{
    /**
     * Whether the value has changed since creation
     * @var bool
     */
    protected $dirty = false;

    /**
     * Whether or not the value has been unsealed
     * @var bool
     */
    protected $isUnsealed = false;

    /**
     * The plaintext form of the value after unsealing.
     *
     * To apply any operations to this value (eg to `unserialize`), @see AbstractEncryptedValue::decodeForUnsealing()
     *
     * @var mixed|null
     */
    protected $plainTextValue;

    /**
     * The encrypted cipher text for the stored value.
     *
     * To convert the stored plaintext value in to a string prior to encryption, @see AbstractEncryptedValue::encodeForSealing()
     *
     * @var string|null
     * @ORM\Column(type="string")
     */
    protected $cipherText;

    /**
     * The name of the key profile used to encrypt the value. This is used
     * to pick the profile to use when unsealing and resealing the value,
     * and is persisted alongside the cipher text.
     *
     * @var string|null
     * @ORM\Column(type="string")
     */
    protected $keyProfile;

    /**
     * Called to produce the result which will be encrypted.
     *
     * @param $plainTextValue
     * @return mixed
     */
    protected abstract function encodeForSealing($plainTextValue);

    /**
     * Called to convert the decrypted value in to its correct representation (eg an array)
     *
     * @param $plainText
     * @return mixed
     */
    protected abstract function decodeForUnsealing($plainText);

    /**
     * Gets the plaintext value
     *
     * @return null|mixed
     * @throws KeyProfileNotFoundException
     */
    public function getValue()
    {
        if(!$this->isUnsealed)
        {
            $this->unseal();
        }

        return $this->plainTextValue;
    }

    /**
     * Sets the value that will be held and encrypted
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->plainTextValue = $value;
        $this->clearCipherValue();
    }

    /**
     * Seals the value, using the named key profile
     *
     * @throws KeyProfileNotFoundException
     */
    public function seal()
    {
        // Only re-seal the value if it was changed
        if($this->dirty)
        {
            $profile = $this->getKeyProfileInstance();

            // If the key profile currently in use needs rolling,
            // take the opportunity to do so now.
            if($profile->shouldBeRolled())
            {
                // Use the default key, instead of the existing one
                $profile = $this->getDefaultKeyProfile();
            }

            $this->cipherText = $profile->encrypt($this->encodeForSealing($this->plainTextValue));
            $this->keyProfile =  $profile->getName();

            $this->plainTextValue = null;
            $this->isUnsealed = false;
            $this->dirty = false;
        }
    }

    /**
     * Unseals the value, using the named key profile
     *
     * @throws KeyProfileNotFoundException
     */
    public function unseal()
    {
        // Only unseal if the value hasn't been changed,
        // and it hasn't already been unsealed
        if(!$this->dirty && !$this->isUnsealed)
        {
            $profile = $this->getKeyProfileInstance();
            $this->plainTextValue = $this->decodeForUnsealing(
                $profile->decrypt($this->cipherText)
            );
            $this->isUnsealed = true;
        }
    }

    private function clearCipherValue()
    {
        $this->cipherText = null;
        $this->dirty = true;
        $this->isUnsealed = true;
    }

    /**
     * @return KeyProfileInterface
     * @throws KeyProfileNotFoundException
     */
    private function getKeyProfileInstance()
    {
        // If no key profile has been set yet (because the value
        // has never been encrypted before), use the default profile
        if($this->keyProfile == null)
        {
            return $this->getDefaultKeyProfile();
        }

        $profile = KeyProfileLoader::load($this->keyProfile);
        if(!$profile)
        {
            throw new KeyProfileNotFoundException($this->keyProfile);
        }

        return $profile;
    }

    private function getDefaultKeyProfile()
    {
        return KeyProfileLoader::getDefaultKeyProfile();
    }
}