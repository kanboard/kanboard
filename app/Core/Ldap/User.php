<?php

namespace Kanboard\Core\Ldap;

/**
 * LDAP User
 *
 * @package ldap
 * @author  Frederic Guillot
 */
class User
{
    /**
     * Query
     *
     * @access private
     * @var Query
     */
    private $query;

    /**
     * Constructor
     *
     * @access public
     * @param  Query   $query
     */
    public function __construct(Query $query = null)
    {
        $this->query = $query ?: new Query;
    }

    /**
     * Get user profile
     *
     * @access public
     * @param  resource  $ldap
     * @param  string    $baseDn
     * @param  string    $query
     * @return array
     */
    public function getProfile($ldap, $baseDn, $query)
    {
        $this->query->execute($ldap, $baseDn, $query, $this->getAttributes());
        $profile = array();

        if ($this->query->hasResult()) {
            $profile = $this->prepareProfile();
        }

        return $profile;
    }

    /**
     * Build user profile
     *
     * @access private
     * @return boolean|array
     */
    private function prepareProfile()
    {
        return array(
            'ldap_id' => $this->query->getAttribute('dn', ''),
            'username' => $this->query->getAttributeValue($this->getAttributeUsername()),
            'name' => $this->query->getAttributeValue($this->getAttributeName()),
            'email' => $this->query->getAttributeValue($this->getAttributeEmail()),
            'is_admin' => (int) $this->isMemberOf($this->query->getAttribute($this->getAttributeGroup(), array()), $this->getGroupAdminDn()),
            'is_project_admin' => (int) $this->isMemberOf($this->query->getAttribute($this->getAttributeGroup(), array()), $this->getGroupProjectAdminDn()),
            'is_ldap_user' => 1,
        );
    }

    /**
     * Check group membership
     *
     * @access public
     * @param  array   $group_entries
     * @param  string  $group_dn
     * @return boolean
     */
    public function isMemberOf(array $group_entries, $group_dn)
    {
        if (! isset($group_entries['count']) || empty($group_dn)) {
            return false;
        }

        for ($i = 0; $i < $group_entries['count']; $i++) {
            if ($group_entries[$i] === $group_dn) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ge the list of attributes to fetch when reading the LDAP user entry
     *
     * Must returns array with index that start at 0 otherwise ldap_search returns a warning "Array initialization wrong"
     *
     * @access public
     * @return array
     */
    public function getAttributes()
    {
        return array_values(array_filter(array(
            $this->getAttributeUsername(),
            $this->getAttributeName(),
            $this->getAttributeEmail(),
            $this->getAttributeGroup(),
        )));
    }

    /**
     * Get LDAP account id attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeUsername()
    {
        return LDAP_ACCOUNT_ID;
    }

    /**
     * Get LDAP account email attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeEmail()
    {
        return LDAP_ACCOUNT_EMAIL;
    }

    /**
     * Get LDAP account name attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeName()
    {
        return LDAP_ACCOUNT_FULLNAME;
    }

    /**
     * Get LDAP account memberof attribute
     *
     * @access public
     * @return string
     */
    public function getAttributeGroup()
    {
        return LDAP_ACCOUNT_MEMBEROF;
    }

    /**
     * Get LDAP admin group DN
     *
     * @access public
     * @return string
     */
    public function getGroupAdminDn()
    {
        return LDAP_GROUP_ADMIN_DN;
    }

    /**
     * Get LDAP project admin group DN
     *
     * @access public
     * @return string
     */
    public function getGroupProjectAdminDn()
    {
        return LDAP_GROUP_PROJECT_ADMIN_DN;
    }
}
