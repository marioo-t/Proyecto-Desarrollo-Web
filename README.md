# MarianOpen

**Tus tutorías, abiertas y de código abierto.**

MarianOpen es un sistema web para gestionar tutorías académicas. Los profesores publican tutorías por materia y tema, con fecha, horario y un cupo mínimo y máximo; los estudiantes las buscan, solicitan un lugar y siguen el estado de su solicitud; y al terminar, el profesor registra asistencia y nota. Un administrador gestiona materias y cuentas.

Proyecto final del curso de Desarrollo Web — Universidad Mariano Gálvez de Guatemala, 2026.

---

## Funcionalidades

| Rol | Puede |
|---|---|
| **Estudiante** | Registrarse; buscar tutorías por materia, tema, fecha, profesor o estado; solicitar un lugar; cancelar antes de que empiece; ver su historial con notas y comentarios. |
| **Profesor** | Crear, editar, confirmar, cancelar o eliminar sus tutorías; aceptar o rechazar solicitudes; ver a sus estudiantes inscritos; registrar asistencia, nota y comentario. |
| **Administrador** | Crear profesores; activar o desactivar cuentas; crear y eliminar materias; gestionar cualquier tutoría; ver los totales del sistema. |

Todos los roles tienen un **panel** con indicadores propios y la página **Mi cuenta** para cambiar su contraseña.

### Reglas principales

- Un profesor no puede tener dos tutorías que se crucen en horario.
- Solo las solicitudes **aceptadas** ocupan cupo, y nunca se supera el cupo máximo.
- Una tutoría solo se confirma al alcanzar el cupo mínimo; si alguien cancela y baja del mínimo, vuelve a estar abierta.
- Cuando pasa la hora de fin, la tutoría se muestra como **finalizada** y se puede calificar.
- La nota va de 0 a 100; se aprueba con **61**. Si el estudiante no asistió, queda reprobado.
- Una tutoría con inscritos no se elimina, se cancela. Una materia con tutorías no se elimina.

## Tecnologías

- **Servidor:** PHP 8 (sin frameworks) con PDO
- **Base de datos:** MySQL 8 o MariaDB 10.4+
- **Interfaz:** HTML, CSS (mobile first) y JavaScript puro con `fetch`
- **Servidor web:** Apache (XAMPP, WAMP o similar) con `AllowOverride` habilitado para que se apliquen los `.htaccess`

## Estructura

```
├── index.php, registro.php        Inicio de sesión y registro (públicas)
├── panel.php, tutorias.php,       Páginas con sesión, protegidas por rol
│   tutoria.php, mis-solicitudes.php, estudiantes.php,
│   profesores.php, alumnos.php, materias.php, cuenta.php
├── js/                            api.js (cliente de la API), sesion.js (menú) y uno por página
├── css/estilos.css                Estilos (768 px tablet, 1024 px escritorio)
├── img/, favicon.png              Logo, fondos e ícono
├── api/                           Endpoints JSON: login, registro, sesion, logout, tutorias,
│                                  solicitudes, notas, panel, materias, profesores, alumnos,
│                                  estudiantes, cuenta
├── api/core/                      Clases internas: Auth, Pagina, Usuarios, Cupos, Notas,
│                                  Env, Request, Response (no accesibles desde el navegador)
└── tutorias.sql                   Crea la base sistema_tutorias con datos de prueba
```

Todas las respuestas de la API tienen el formato:

```json
{ "success": true, "message": "OK", "data": { } }
```

## Instalación

1. **Copiar el proyecto** dentro de la carpeta pública del servidor (por ejemplo `C:\xampp\htdocs\marianopen`).

2. **Crear la base de datos** importando `tutorias.sql` desde phpMyAdmin o con:

   ```bash
   mysql -u root -p < tutorias.sql
   ```

   > El script **borra y vuelve a crear** la base `sistema_tutorias`. Las fechas de prueba se calculan desde el día en que se importa.

3. **Crear el archivo `api/core/.env`** con los datos de conexión:

   ```ini
   HOST_DB=localhost
   PORT_DB=3306
   USER_DB=root
   PASSWORD_DB=
   DATABASE=sistema_tutorias
   CHARSET=utf8mb4

   COOKIE_NAME=TUTORIAS_SID
   COOKIE_KEY=cambia-esto-por-una-clave-larga-y-aleatoria
   # 1 si el sitio usa HTTPS; 0 para pruebas en http://localhost
   COOKIE_SECURE=0
   ```

   `COOKIE_KEY` firma la cookie de «Recordarme»; usa una cadena larga y aleatoria (por ejemplo, la salida de `php -r "echo bin2hex(random_bytes(32));"`). Este archivo no se sube al repositorio.

4. **Abrir** `http://localhost/marianopen/` en el navegador.

## Cuentas de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | admin@uni.edu | admin123 |
| Profesor | ana@uni.edu, luis@uni.edu | profesor123 |
| Estudiante | carlos@uni.edu, maria@uni.edu, jose@uni.edu, sofia@uni.edu, diego@uni.edu, lucia@uni.edu | estudiante123 |

Cambia estas contraseñas (o no importes los datos de prueba) antes de publicar el sitio.

## Seguridad

- Contraseñas cifradas con `password_hash` / `password_verify`.
- Permisos por rol en cada página (`Pagina::privada`) y en cada endpoint (`Auth::requiere`); una cuenta desactivada pierde la sesión.
- Consultas preparadas con PDO en toda la API.
- Texto escapado antes de mostrarse en pantalla (protección XSS).
- Transacciones con `SELECT … FOR UPDATE` al solicitar, aceptar, cancelar y calificar, para no superar los cupos.
- Cookie «Recordarme» de 30 días firmada con HMAC-SHA256; cookies `HttpOnly` y `SameSite=Strict`.
- Límite de intentos de inicio de sesión: 5 fallos por correo o 20 por IP en 15 minutos.
- `.htaccess` bloquea el listado de carpetas, los archivos ocultos (`.env`, `.git`), `*.sql`, `*.md` y la carpeta `api/core`.

## Equipo

| Integrante | Responsabilidades principales | Portafolio |
|---|---|---|
| **Dylan Rene Hernández Recinos** | Base de datos, núcleo de la API, autenticación, cliente JS de la API, reglas de tutorías, notas y asistencia | [dylanherdz.github.io](https://dylanherdz.github.io/) |
| **Gabriel André Guillén Pocón** | Prototipo y diseño, control de acceso por rol, materias y cupos, solicitudes, administración, identidad visual y seguridad | [gguillenp.github.io/portafolio-gabrielg](https://gguillenp.github.io/portafolio-gabrielg/) |
| **Mario Alberto Taracena Pérez** | Repositorio, formularios y menú por rol, pantallas de tutorías y solicitudes, panel, API de usuarios y revisión final | [marioo-t.github.io/portafolio-mario](https://marioo-t.github.io/portafolio-mario/) |

El trabajo se organizó en seis fases (19 de agosto – 9 de noviembre de 2026) con revisiones mediante pull requests. El detalle de tareas, fechas y commits está en el cronograma del proyecto.
