<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">

    <!-- 🔝 HEADER -->
    <h1 class="wp-heading-inline">Clientes</h1>

    <a href="<?php echo admin_url('admin.php?page=courier-clients&action=new'); ?>"
       class="page-title-action">
        + Nuevo Cliente
    </a>

    <hr class="wp-header-end">

    <!-- 🔍 FILTROS -->
    <form method="get" style="margin-bottom:15px;">

        <input type="hidden" name="page" value="courier-clients">

        <input type="text"
               name="s"
               placeholder="Buscar cliente..."
               value="<?php echo esc_attr($_GET['s'] ?? ''); ?>">

        <select name="type">
            <option value="">Todas las categorías</option>

            <option value="individual"
                <?php selected($_GET['type'] ?? '', 'individual'); ?>>
                Individual
            </option>

            <option value="company"
                <?php selected($_GET['type'] ?? '', 'company'); ?>>
                Empresa
            </option>
        </select>

        <button class="button">Filtrar</button>

        <a class="button"
           href="<?php echo admin_url('admin.php?page=courier-clients'); ?>">
            Limpiar
        </a>

    </form>

    <!-- ⚡ FORM PRINCIPAL -->
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">

        <input type="hidden" name="action" value="courier_clients_bulk">

        <!-- 📋 TABLA -->
        <table class="wp-list-table widefat striped">

            <thead>
                <tr>
                    <th width="30">
                        <input type="checkbox" id="select_all">
                    </th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($clients)) : ?>

                <?php foreach ($clients as $client) : ?>

                    <tr>

                        <!-- CHECKBOX -->
                        <td>
                            <input type="checkbox"
                                   class="client_check"
                                   name="client_ids[]"
                                   value="<?php echo esc_attr($client->id); ?>">
                        </td>

                        <td><?php echo esc_html($client->id); ?></td>

                        <td>
                            <?php echo esc_html($client->first_name . ' ' . $client->last_name); ?>
                        </td>

                        <td><?php echo esc_html($client->email); ?></td>
                        <td><?php echo esc_html($client->phone); ?></td>
                        <td><?php echo esc_html($client->client_type); ?></td>

                        <!-- ✏ / 🗑 ACCIONES INDIVIDUALES -->
                        <td>

                            <a class="button button-small"
                               href="<?php echo admin_url('admin.php?page=courier-clients&action=view&id=' . $client->id); ?>">
                                Ver
                            </a>

                            <a class="button button-small"
                               href="<?php echo admin_url('admin.php?page=courier-clients&action=edit&id=' . $client->id); ?>">
                                Editar
                            </a>

                            <a class="button button-small button-link-delete"
                               href="<?php echo admin_url('admin-post.php?action=courier_delete_client&id=' . $client->id); ?>"
                               onclick="return confirm('¿Eliminar este cliente?');">
                                Eliminar
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else : ?>

                <tr>
                    <td colspan="7">No hay clientes registrados</td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

        <!-- ⚡ ACCIONES MASIVAS (SIEMPRE VISIBLES) -->
        <div style="margin-top:15px;">

            <select name="bulk_action" id="bulk_action">

                <option value="">Acciones</option>

                <option value="edit" id="edit_option">
                    Editar
                </option>

                <option value="delete">
                    Eliminar
                </option>

            </select>

            <button class="button button-primary">
                Aplicar
            </button>

        </div>

    </form>

</div>

<!-- ⚡ JS LÓGICO -->
<script>

const checkboxes = document.querySelectorAll('.client_check');
const selectAll = document.getElementById('select_all');
const editOption = document.getElementById('edit_option');
const bulkAction = document.getElementById('bulk_action');

function getCheckedCount() {
    return document.querySelectorAll('.client_check:checked').length;
}

function updateUI() {

    let count = getCheckedCount();

    // 🧠 REGLA: EDITAR SOLO SI HAY 1 SELECCIONADO
    if (count === 1) {
        editOption.style.display = 'block';
        editOption.disabled = false;
    } else {
        editOption.style.display = 'none';
        editOption.disabled = true;

        // evitar selección inválida
        if (bulkAction.value === 'edit') {
            bulkAction.value = '';
        }
    }
}

// SELECT ALL
selectAll.addEventListener('change', function () {
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateUI();
});

// INDIVIDUALES
checkboxes.forEach(cb => {
    cb.addEventListener('change', updateUI);
});

// INIT
updateUI();

</script>