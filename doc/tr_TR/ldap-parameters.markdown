LDAP Yapılandırma Parametreleri
=============================

Kullanılabilir LDAP parametrelerinin listesi aşağıdadır:

| Parametre                       | Varsayılan değer | Açıklama                                       |
|---------------------------------|------------------|------------------------------------------------|
| `LDAP_AUTH`                     | false            | LDAP kimlik doğrulamasını etkinleştir          |
| `LDAP_SERVER`                   | Empty            | LDAP sunucusu ana makine adı                   |
| `LDAP_PORT`                     | 389              | LDAP server port                               |
| `LDAP_SSL_VERIFY`               | true             | `ldaps://` stil URL'si için sertifikayı doğrulama |
| `LDAP_START_TLS`                | false            | LDAP başlangıç TLS'i etkinleştir                  |
| `LDAP_USERNAME_CASE_SENSITIVE`  | false            | Kanboard, yinelenen kullanıcılardan kaçınmak için ldap kullanıcı adını küçük harflerle yazmaktadır (veritabanı büyük/küçük harf duyarlıdır)  |
| `LDAP_BIND_TYPE`                | anonymous        | Bağ türü: "anonim", "kullanıcı" veya "vekil"     |
| `LDAP_USERNAME`                 | null             | Kullanıcı moduyla kullanılacak proxy modu veya kullanıcı adı kalıbıyla kullanılacak LDAP kullanıcı adı |
| `LDAP_PASSWORD`                 | null             | Proxy modu için kullanılacak LDAP şifresi            |
| `LDAP_USER_BASE_DN`             | Empty            | Kullanıcılar için LDAP DN (Örnek: "CN=Kullanıcılar,DC=kanboard,DC=yerel")          |
| `LDAP_USER_FILTER`              | Empty            | Kullanıcı hesabı ararken kullanılacak LDAP deseni (Örnek: "(&(objectClass=user)(sAMAccountName=%s))")            |
| `LDAP_USER_ATTRIBUTE_USERNAME`  | uid              | Kullanıcı adı için LDAP özelliği (Örnek: "samaccountname")            |
| `LDAP_USER_ATTRIBUTE_FULLNAME`  | cn               | Kullanıcı tam adı için LDAP özelliği (Örnek: "displayname")         |
| `LDAP_USER_ATTRIBUTE_EMAIL`     | mail             | Kullanıcı e-postası için LDAP özelliği                                     |
| `LDAP_USER_ATTRIBUTE_GROUPS`    | memberof         | Kullanıcı profilinde gruplar bulmak için LDAP özniteliği                     |
| `LDAP_USER_ATTRIBUTE_PHOTO`     | Empty            | Kullanıcı fotoğrafını bulmak için LDAP özniteliği (jpegPhoto veya thumbnailPhoto)                      |
| `LDAP_USER_ATTRIBUTE_LANGUAGE`  | Empty            | Kullanıcı dili için LDAP özniteliği (preferredlanguage), kabul edilen dil biçimi "fr-FR" |
| `LDAP_USER_CREATION`            | true             | Otomatik LDAP kullanıcı yaratmayı etkinleştir                               |
| `LDAP_GROUP_ADMIN_DN`           | Empty            | Yöneticiler için LDAP DN (Örnek: "CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local")   |
| `LDAP_GROUP_MANAGER_DN`         | Empty            | Müdürler için LDAP DN (Örnek: "CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local")   |
| `LDAP_GROUP_PROVIDER`           | false            | Proje izinleri için LDAP grup sağlayıcısını etkinleştirin  |
| `LDAP_GROUP_BASE_DN`            | Empty            | Gruplar için LDAP Tabanı DN  |
| `LDAP_GROUP_FILTER`             | Empty            | LDAP grup filitresi (Örnek: "(&(objectClass=group)(sAMAccountName=%s*))")   |
| `LDAP_GROUP_USER_FILTER`        | Empty            | Kanboard, tanımlandıysa, LDAP_GROUP_BASE_DN 'deki kullanıcı gruplarını bu filtre ile arayacaktır, yalnızca posixGroups için kullanışlıdır (Örnek: `(&(objectClass=posixGroup)(memberUid=%s))`) |
| `LDAP_GROUP_ATTRIBUTE_NAME`     | cn               | Grup adı için LDAP özniteliği  |

Notlar:

- LDAP nitelikleri küçük harflerle yazılmış olmalıdır
