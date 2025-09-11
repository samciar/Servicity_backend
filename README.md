# Servicity

**Servicity** es una aplicación móvil que conecta personas que necesitan servicios con quienes pueden ofrecerlos, como plomeros, electricistas, niñeras, entre otros. Inspirado en plataformas como TaskRabbit, Servicity está pensado para el contexto colombiano, con un enfoque en la economía colaborativa local y el acceso fácil desde cualquier región del país.

Este proyecto forma parte del programa de formación **Análisis y Desarrollo de Software** del **SENA**, desarrollado por aprendices como solución a necesidades reales en nuestras comunidades.

## 🚀 Objetivo del proyecto

Desarrollar una aplicación que facilite la búsqueda, publicación y contratación de servicios entre personas dentro de Colombia, promoviendo:

- El emprendimiento local.
- La generación de ingresos a través de servicios informales.
- El acceso rápido y confiable a trabajadores calificados.
- Una interfaz sencilla e intuitiva para todo tipo de usuario.

## 🧠 Características principales

- Registro de usuarios como **cliente** o **prestador de servicios**.
- Búsqueda por categorías de servicios (ej. aseo, reparaciones, cuidado de personas, etc.).
- Geolocalización para encontrar servicios cercanos.
- Sistema de calificaciones y comentarios.
- Panel de administración (en desarrollo).

## 🛠️ Tecnologías utilizadas

- **Figma** – Prototipo de la interfaz.
- **Flutter** – Desarrollo de la app móvil (en progreso).
- **React js** - Creación de las interfaces dínamicas SPA.
- **Tailwind** - Aplicación de estilos, manejo de temas y colores corporativos en el frontend.
- **Laravel** - Construcción del backend (propuesto).
- **Firebase** – Autenticación oAuth (propuesto).
- **GitHub Projects** – Gestión del proyecto.

## 📅 Estado del proyecto

🚧 **Fase de desarrollo inicial**  
Actualmente estamos trabajando en el diseño de interfaces y estructura funcional básica. La integración de bases de datos y lógica backend será implementada en las siguientes etapas del proyecto.

## 👨‍💻 Equipo de desarrollo

Este proyecto está siendo desarrollado por aprendices del **SENA** como parte del programa de formación técnica en **Análisis y Desarrollo de Software**.

## 🚀 Instalación y configuración

Sigue estos pasos para configurar y ejecutar el proyecto localmente:

### Prerrequisitos
- **PHP 8.2** o superior
- **Composer** - Gestor de dependencias de PHP
- **Node.js** y **npm** - Para dependencias de JavaScript (opcional)
- **Base de datos** (SQLite por defecto, o MySQL/PostgreSQL configurados)

### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio>
cd Servicity_backend
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Instalar dependencias de JavaScript (opcional)
```bash
npm install
```

### 4. Configurar variables de entorno
```bash
cp .env.example .env
```

Edita el archivo `.env` y configura las variables según tu entorno, especialmente:
- `APP_NAME` - Nombre de la aplicación
- `APP_URL` - URL de la aplicación
- Configuración de base de datos (por defecto usa SQLite)

### 5. Generar clave de aplicación
```bash
php artisan key:generate
```

### 6. Configurar base de datos
Para SQLite (configuración por defecto):
```bash
touch database/database.sqlite
```

Para MySQL/PostgreSQL, configura las variables de entorno en `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=servicity
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Ejecutar migraciones
```bash
php artisan migrate:refresh
```

### 8. Ejecutar seeders (Seeders obligatorios)
```bash
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=MunicipalitySeeder
```

Para ejecutar todos los seeders:
```bash
php artisan db:seed
```

### 9. Iniciar el servidor de desarrollo
```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

### Comandos adicionales útiles
- **Limpiar cache**: `php artisan optimize:clear`
- **Ver rutas disponibles**: `php artisan route:list`
- **Ejecutar tests**: `php artisan test`

## 📄 Licencia

Este proyecto está bajo la licencia [MIT](LICENSE).

---

> Desarrollado con 💚 por aprendices SENA – Colombia
