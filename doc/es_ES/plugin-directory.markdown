Plugin de configuración del directorio
======================================

Para instalar, actualizar y eliminar plugins dede la interface de usuario, debes tener estos requerimientos:

- El directorio del plugin debe ser de escritura por el usuario del servidor web
- La extensión Zip debe estar disponible en tu server.
- Los parametros de configuración `PLUGIN_INSTALLER` deben estar en `true`

Para desactivar esta función , cambie el valor de `PLUGIN_INSTALLER` a `false` en tu archivo de configuración.
También puede cambiar los permisos de la carpeta Plugin en el filesystem.

Sólo los administradores pueden instalar plugins desde la interfaz de usuario.

Por defecto, sólo plug-in que aparece en la página web de Kanboard están disponibles .
