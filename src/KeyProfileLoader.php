<?php

namespace APHunt\EncryptedEmbeddables;

/**
 * Class KeyProfileLoader
 *
 * Provides static access to defined key profiles, so that
 * @see AbstractEncryptedValue can access key profiles without
 * injection.
 *
 * @package APHunt\EncryptedEmbeddables
 * @static
 */
class KeyProfileLoader
{
    /**
     * @var KeyProfileInterface[]
     */
    private static $_profiles = [];

    /**
     * @var KeyProfileInterface
     */
    private static $_defaultProfile;

    /**
     * Registers a key profile
     *
     * @param KeyProfileInterface $keyProfile
     */
    public static function registerKeyProfile(KeyProfileInterface $keyProfile)
    {
        if(empty(self::$_profiles))
        {
            self::setDefaultKeyProfile($keyProfile);
        }
        self::$_profiles[$keyProfile->getName()] = $keyProfile;
    }

    /**
     * Load a key profile by its name. Returns null if not found.
     *
     * @param string $profileName
     * @return KeyProfileInterface|null
     */
    public static function load(string $profileName)
    {
        if(isset(self::$_profiles[$profileName]))
        {
            return self::$_profiles[$profileName];
        }

        return null;
    }

    /**
     * Sets the key profile which will be used for encryption when no key
     * profile has been set (ie the first time a value is stored).
     *
     * This should be the profile of the current key, if using key rolling.
     *
     * @param KeyProfileInterface $keyProfile
     */
    public static function setDefaultKeyProfile(KeyProfileInterface $keyProfile)
    {
        self::$_defaultProfile = $keyProfile;
    }

    /**
     * Gets the default key profile.
     *
     * @return KeyProfileInterface
     */
    public static function getDefaultKeyProfile() : KeyProfileInterface
    {
        return self::$_defaultProfile;
    }
}