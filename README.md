# Shoply Multi-Tenant E-commerce Platform

Shoply es una plataforma de comercio electrónico multi-tenant de alto rendimiento construida con **Laravel 12**, **Livewire**, **PHP 8.2**, **Alpine.js** y **Tailwind CSS**. Permite a múltiples administradores gestionar sus propias tiendas independientes con control centralizado por parte de un Superadministrador.

---

## ✨ Características Principales

### 👑 Panel de Superadministrador (`/superadmin/modules`)
Un centro de control exclusivo y altamente estético con diseño premium y microanimaciones para gestionar los tenants:
- **Gestión de Módulos y Métricas (Planes)**:
  - Asignación rápida de módulos mediante **Plan Básico** (catálogo básico, órdenes, portadas y KPIs esenciales) y **Plan Premium** (habilita el 100% de la plataforma).
  - Toggles granulares para activar/desactivar módulos individuales y categorías completas de KPIs de negocio.
- **Configuración de Identidad de Tienda**:
  - Personalización de nombre de fantasía, logo, moneda local y número de WhatsApp de contacto.
- **Gestión de Usuarios del Sistema**:
  - Alta, edición, desactivación temporal y eliminación de usuarios por tienda.
  - **Seguridad Garantizada**: Bloqueo absoluto a la modificación de roles de usuarios preexistentes para prevenir elevación de privilegios de forma indebida.

### 📊 Límites de Recursos y Facturación (Cuotas de Suscripción)
- Control estricto de cuotas en base al plan asignado:
  - **Límite de Productos**: Límite máximo de catálogo por tienda.
  - **Límite de Usuarios**: Límite máximo de colaboradores.
  - **Límite de Pedidos Mensuales**: Límite de ventas mensuales de la tienda.
- Facturación clara que incluye precio del plan, ciclo de facturación (mensual, trimestral, anual) y fecha de próximo vencimiento.
- Bloqueo dinámico e inteligente en la pasarela de compra (checkout) si la tienda excede su límite mensual de pedidos.

### 📝 Historial de Actividad y Auditoría (Audit Log)
- Registros persistentes de auditoría ante cualquier cambio crítico en el tenant.
- Guardado atómico de variables en formato JSON (valores anteriores vs. valores nuevos).
- Visualizador interactivo detallado con formato monospaciado dentro del panel para comparar cambios históricos.

### 🕒 Ventanas de Mantenimiento Programadas
- Programación de fecha y hora exacta de inicio y fin de mantenimiento.
- Activación automatizada por middleware.
- Interfaz pública con **cuenta regresiva activa en tiempo real** (días, horas, minutos, segundos) que se refresca automáticamente al finalizar el tiempo, además de enlaces directos de soporte vía Mail y WhatsApp.

---

## 🛠️ Tecnologías y Stack
- **Backend:** Laravel 12 & PHP 8.2.12
- **Frontend:** Livewire 3, Alpine.js, Tailwind CSS
- **Base de Datos:** MySQL / MariaDB (con soporte multi-tenant y bloqueo pesimista `lockForUpdate` para evitar sobreventas)
- **Modales & Notificaciones:** SweetAlert2 y FontAwesome 6

---

## 🚀 Instalación y Servidor Local

1. Clonar el repositorio e instalar dependencias de Composer:
   ```bash
   composer install
   ```

2. Instalar dependencias de Node.js y compilar assets:
   ```bash
   npm install
   npm run dev
   ```

3. Configurar el archivo `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Ejecutar las migraciones y seeders:
   ```bash
   php artisan migrate --seed
   ```

5. Iniciar el servidor local:
   ```bash
   php artisan serve
   ```
