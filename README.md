
# Proyecto [Lavadero Morita]

Este proyecto está desarrollado con **Laravel 12**, **Vue 3** y **Inertia.js**.

## 🚀 Requisitos Previos

- PHP >= 8.2  
- Composer (https://getcomposer.org/)  
- Node.js >= 18 + npm o yarn (https://nodejs.org/)  
- MySQL
- Servidor local como XAMPP.

## ⚙️ Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/ValentinRuppel/lavadero-morita.git
cd nombre-repositorio
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node

```bash
npm install
```

### 4. Configurar el archivo `.env`

Duplicá el archivo de ejemplo `.env.example` y renombralo a `.env`:

```bash
cp .env.example .env
```

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Configurar la base de datos

Editá el archivo `.env` y completá los datos de conexión a la base de datos:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lavadero-morita
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Ejecutar migraciones y seeders (si tiene)

```bash
php artisan migrate
```

> Si hay datos de prueba:
```bash
php artisan db:seed
```

### Backend (Laravel)

```bash
php artisan serve
```

### Frontend (Vite + Inertia + Vue)

En otra terminal:

```bash
npm run dev
```

## Acceso a la app web

Por defecto la aplicación estará en:

- **Backend (API + Laravel + Inertia):** [http://127.0.0.1:8000]
- El frontend está integrado con Inertia, así que todo se renderiza desde Laravel.

## Estructura del proyecto

- `/resources/js/` → Componentes Vue  
- `/resources/views/` → Vistas Blade base para Inertia  
- `/routes/web.php` → Rutas web  
- `/app/` → Controladores, modelos y lógica backend  

---

## Tecnologías usadas

- Laravel 12  
- Vue 3  
- Inertia.js  
- Vite  


## 🤝 Contacto

Cualquier duda sobre el funcionamiento o la instalación del proyecto, puede comunicarse con nosotros a través de [muntuy75@gmail.com] o [reloren1918@gmail.com].
