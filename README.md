# Proyecto: Sistema de Gestión de Archivos en Laravel

## 1️⃣ Descripción

Este proyecto es una aplicación web desarrollada en **Laravel 12** que permite a los usuarios autenticados **subir, visualizar, administrar y controlar archivos personales**, respetando cuotas de almacenamiento predefinidas. Cada usuario posee un espacio asignado en el servidor y puede gestionar sus archivos de forma intuitiva desde una interfaz web limpia y dinámica.

La aplicación está diseñada para funcionar sobre un entorno local configurado con **XAMPP**, utilizando **MySQL** como base de datos y **Blade** como motor de plantillas para la interfaz.

### Objetivos principales

* Proporcionar una solución modular y escalable para la gestión de archivos.
* Garantizar un control de almacenamiento personalizado por usuario.
* Facilitar la interacción mediante un frontend ligero y dinámico.
* Promover buenas prácticas de arquitectura MVC y programación limpia.

### Funcionalidades principales

* **Autenticación de usuarios** (login, registro y roles diferenciados).
* **Subida de archivos** mediante un formulario con validación de tamaño y tipo.
* **Control de cuota de almacenamiento por usuario**, evitando superar el límite asignado.
* **Visualización de archivos** en una tabla con detalles como nombre, tipo y tamaño.
* **Actualización dinámica** de la tabla al subir nuevos archivos sin recargar la página.
* **Interfaz moderna y responsiva** implementada con Tailwind CSS.
* **Gestión de relaciones**: un usuario puede tener varios archivos (relación *uno a muchos*).

## 2️⃣ Decisiones de diseño tomadas

* **Laravel + Blade + JavaScript puro**: combinación que ofrece estructura sólida y simplicidad, sin necesidad de frameworks frontend pesados.
* **Arquitectura MVC**: se separan claramente los modelos, controladores y vistas para un mantenimiento más eficiente.
* **Eloquent ORM**: se utiliza para manejar las relaciones `User -> File` y facilitar operaciones CRUD.
* **Cuota de almacenamiento**: implementada como método en el modelo `User`, permitiendo cálculos directos sobre el espacio usado y disponible.
* **Validación en backend**: todos los archivos son verificados antes de guardarse para evitar cargas corruptas o no permitidas.
* **JavaScript modular**: el manejo de eventos y la actualización de la tabla se delegan a un archivo JS externo (`upload.js`) para mantener las vistas limpias.
* **Seguridad**: se implementan tokens CSRF en los formularios para evitar ataques de tipo Cross-Site Request Forgery.

## 3️⃣ Instalación y configuración con XAMPP

### Requisitos previos

* XAMPP (Apache + MySQL) instalado.
* PHP 8.3+ incluido en XAMPP.
* Laravel 12.
* Composer instalado.
* Node.js y npm instalados.

### Pasos de instalación

1. **Instalar XAMPP** desde [https://www.apachefriends.org/es/index.html](https://www.apachefriends.org/es/index.html) y arrancar los servicios de Apache y MySQL.

2. **Clonar el proyecto** dentro de la carpeta `htdocs`:

   * `git clone https://github.com/migueleaf28/upload-system-mvc.git`

3. **Configurar el archivo `.env`** con los datos de conexión de la base de datos local.

4. **Generar la clave de aplicación** para Laravel ejecutando `php artisan key:generate`.

5. **Crear la base de datos** (por ejemplo `db_crud`) desde phpMyAdmin.

6. **Ejecutar las migraciones y seeders** con `php artisan migrate --seed` para crear tablas y usuarios de ejemplo.

7. **Instalar dependencias frontend** ejecutando `npm install` y luego `npm run dev`.

8. **Configuración del `.env`**

   **Copiar el archivo de ejemplo:**

   cp .env.example .env

   **Configurar la base de datos en `.env`:**

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_crud
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   **Generar clave de aplicación:**

   ```bash
   php artisan key:generate
    ```

   Ejecutar las migraciones:

   ```bash
    php artisan migrate
   ```

    Cargar datos de prueba:
   ```bash
    php artisan db:seed
   ```

9. **Iniciar el servidor de desarrollo** con `php artisan serve` y acceder desde el navegador a `http://127.0.0.1:8000`.

## 4️⃣ Credenciales de ejemplo

### Administrador

* **Email:** [admin@gmail.com]
* **Contraseña:** 123456
* **Rol:** Admin

### Usuario

* **Email:** [migueleaf28@gmail.com]
* **Contraseña:** miguel123
* **Rol:** Usuario

## 5️⃣ Uso

1. Iniciar sesión con las credenciales de ejemplo.
2. Acceder a la sección **“Gestión de archivos”** desde el menú principal.
3. Subir un archivo utilizando el formulario correspondiente.
4. Observar cómo la tabla se actualiza automáticamente con la nueva información.
5. Verificar el tamaño del archivo y el uso de espacio disponible.

## 6️⃣ Consideraciones finales

* Limpiar la caché de Laravel y recompilar assets cuando se realicen modificaciones significativas (`php artisan cache:clear`).
* Adaptar el límite de cuota de almacenamiento según las necesidades de cada implementación.
* Este proyecto puede ser la base para un sistema más complejo de almacenamiento en la nube o panel administrativo.

