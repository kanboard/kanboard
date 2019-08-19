This version of kanban has been modified for taking care of authentification AND identification from an external authentification systeme like SSO.

In my case I use it with Mod_mellon (SAML protocol)

Config file for mod_mellon looks like :

	<Location />
	# Add information from the mod_auth_mellon session to the request.
	AuthType Mellon
	MellonEnable "auth"
	MellonSecureCookie On
    MellonUser uid
	MellonMergeEnvVars On
	MellonSubjectConfirmationDataAddressCheck Off

    MellonSPPrivateKeyFile /etc/apache2/mellon/https_kanban.company.com.key
	MellonSPCertFile /etc/apache2/mellon/https_kanban.company.com.cert
	MellonSPentityId "https://kanban.company.com"
        #
        # If you choose to autogenerate metadata, these options 
        # can be used to fill the <Organization> element. They
        # all follow the syntax "option [lang] value":
        MellonOrganizationName "random-service"
        MellonOrganizationDisplayName "en" "IT Service"
        MellonOrganizationDisplayName "fr" "Service informatique"
        MellonOrganizationURL "http://company.com"




		    # IdP metadata. This should be the metadata file you got from the IdP.
	MellonIdPMetadataFile /etc/apache2/mellon/idp-metadata.xml
	
	MellonDiscoveryURL "https://federation.company.com/WAYF/"

	MellonIdPPublicKeyFile "/etc/apache2/mellon/signing-cert-of-company.pem"
	MellonProbeDiscoveryTimeout 1
    MellonSetEnv "MAIL" "urn:oid:0.9.2342.19200300.100.1.3"
	MellonSetEnv "UID" "urn:oid:0.9.2342.19200300.100.1.1"
	MellonSetEnv "CN" "urn:oid:2.5.4.3"
    MellonEndpointPath /mellon
    MellonSetEnvNoPrefix REMOTE_USER NAME_ID
	MellonIdP "IDP"
    </Location>


configuration of kanban will be :

    // Enable/disable the reverse proxy authentication
    define('REVERSE_PROXY_AUTH', TRUE);
    
    // Header name to use for the username
    define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');
    
    // Enable/disable get field from HEADER
    define('REVERSE_PROXY_GET_FIELDS', FALSE);
    define('REVERSE_PROXY_MAIL_HEADER', 'MELLON_MAIL');
    define('REVERSE_PROXY_NAME_HEADER', 'MELLON_CN');
    
    // Default domain to use for setting the email address
    define('REVERSE_PROXY_DEFAULT_DOMAIN', '');

