# RepoEnvios - PHP + MySQL

Aplicativo web sencillo para gestionar envíos.

## Funciones

- Registrar un envío.
- Consultar todos los envíos.
- Editar un envío.
- Eliminar un envío.
- Validación básica de campos.
- Consultas preparadas con PDO.
- Creación automática de la tabla `envios` si no existe.

## Instalación

1. Sube todos los archivos a tu servidor PHP.
2. Abre `config.php`.
3. Coloca las credenciales reales de MySQL:
   - Host
   - Usuario
   - Clave
   - Base de datos
4. Asegúrate de que la base de datos `xxxx_repoenvios` exista. El aplicativo crea la **tabla**, no la base de datos.
5. Abre `index.php` desde el navegador.

### Importante sobre las credenciales

El archivo incluye los datos proporcionados en la solicitud. El host aparece como `mysql-…..` y el nombre/usuario como `xxxx`, por lo que debes sustituirlos por los valores exactos de tu proveedor si esos son datos abreviados.

## Requisitos

- PHP 7.4+ (recomendado PHP 8.x)
- MySQL/MariaDB
- Extensión PHP PDO MySQL habilitada.

## Seguridad

Para producción, evita publicar `config.php` en repositorios públicos y considera usar variables de entorno para las credenciales.
