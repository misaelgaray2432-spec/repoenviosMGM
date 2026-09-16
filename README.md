# RepoEnvios - HTML + PHP + MySQL

Aplicativo web sencillo para gestionar envíos.

## Funciones

- Registrar un envío.
- Consultar todos los envíos.
- Editar un envío.
- Eliminar un envío.
- Validación básica de campos.
- Consultas preparadas con PDO.
- Creación automática de la tabla `envios` si no existe.
- Pantalla principal en `index.html` con API en `api.php`.

## Instalación

1. Sube todos los archivos a tu servidor PHP.
2. Abre `config.php`.
3. Coloca las credenciales reales de MySQL:
   - Host
   - Usuario
   - Clave
   - Base de datos
4. Asegúrate de que la base de datos `misaelgaray_repoenvios` exista. El aplicativo crea la **tabla**, no la base de datos.
5. Abre `index.html` desde el navegador.

## Requisitos

- PHP 7.4+ (recomendado PHP 8.x)
- MySQL/MariaDB
- Extensión PHP PDO MySQL habilitada.

## Seguridad

Para producción, evita publicar `config.php` en repositorios públicos y considera usar variables de entorno para las credenciales.
