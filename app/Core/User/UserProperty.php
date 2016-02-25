<?php

namespace Kanboard\Core\User;

/**
 * User Property
 *
 * @package  user
 * @author   Frederic Guillot
 */
class UserProperty
{
    /**
     * Get filtered user properties from user provider
     *
     * @static
     * @access public
     * @param  UserProviderInterface $user
     * @return array
     */
    public static function getProperties(UserProviderInterface $user)
    {
        $properties = array(
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            $user->getExternalIdColumn() => $user->getExternalId(),
        );

        $properties = array_merge($properties, $user->getExtraAttributes());

        return array_filter($properties, array(__NAMESPACE__.'\UserProperty', 'isNotEmptyValue'));
    }

    /**
     * Filter user properties compared to existing user profile
     *
     * @static
     * @access public
     * @param  array  $profile
     * @param  array  $properties
     * @return array
     */
    public static function filterProperties(array $profile, array $properties)
    {
        $values = array();

        foreach ($properties as $property => $value) {
            if (array_key_exists($property, $profile) && ! self::isNotEmptyValue($profile[$property])) {
                $values[$property] = $value;
            }
        }

        return $values;
    }

    /**
     * Check if a value is not empty
     *
     * @static
     * @access public
     * @param  string $value
     * @return boolean
     */
    public static function isNotEmptyValue($value)
    {
        return $value !== null && $value !== '';
    }
}
