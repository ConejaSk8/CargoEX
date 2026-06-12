<?php if (!defined('ABSPATH')) exit; ?>


<div class="wrap">

    <h1>Courier ERP Dashboard</h1>

    <p>Accesos rápidos al sistema</p>

    <div class="courier-dashboard-grid">

        <!-- 📦 ENVIOS -->
        <a class="dashboard-card"
           href="<?php echo admin_url('admin.php?page=courier-shipments'); ?>">

            <span class="dashicons dashicons-migrate"></span>
            <h2>Envíos</h2>
            <p>Gestión de paquetes y tracking</p>

        </a>

        <!-- 👤 CLIENTES -->
        <a class="dashboard-card"
           href="<?php echo admin_url('admin.php?page=courier-clients'); ?>">

            <span class="dashicons dashicons-groups"></span>
            <h2>Clientes</h2>
            <p>Registro y administración de clientes</p>

        </a>

        <!-- 🚚 REPARTIDORES -->
        <a class="dashboard-card"
           href="<?php echo admin_url('admin.php?page=courier-drivers'); ?>">

            <span class="dashicons dashicons-id"></span>
            <h2>Repartidores</h2>
            <p>Gestión de conductores</p>

        </a>

        <!-- 🗺 RUTAS -->
        <a class="dashboard-card"
           href="<?php echo admin_url('admin.php?page=courier-routes'); ?>">

            <span class="dashicons dashicons-location-alt"></span>
            <h2>Rutas</h2>
            <p>Planificación de entregas</p>

        </a>

    </div>

</div>