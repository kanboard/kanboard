I'm not able to login with Internet Explorer and Microsoft IIS
==============================================================

If you are not able to login and always get the error **"Username or password required"** even if you have entered the right credentials,
that means there is a problem with the session.

For example, this is a known issue if you meet these criteria:

- You are using a domain name with an underscore: `kanboard_something.mycompany.tld`
- You are using Microsoft Windows Server and IIS
- Your browser is Internet Explorer

Solution: **Do not use underscore in the domain name because this is not a valid domain name**.

Explanation: Internet Explorer doesn't accept cookies with a domain name with underscores because it's not valid.

Reference:

- https://support.microsoft.com/en-us/kb/316112
