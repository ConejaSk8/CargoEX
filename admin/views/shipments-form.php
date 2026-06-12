<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$clients_table  = $wpdb->prefix . 'courier_clients';
$status_table   = $wpdb->prefix . 'courier_statuses';

$clients = $wpdb->get_results(
    "SELECT id,
            first_name,
            last_name
     FROM $clients_table
     ORDER BY first_name ASC"
);

$statuses = $wpdb->get_results(
    "SELECT *
     FROM $status_table
     ORDER BY id ASC"
);

$is_edit = !empty($shipment->id);

?>

<div class="wrap">

    <h1>
        <?php echo $is_edit ? 'Editar Envío' : 'Nuevo Envío'; ?>
    </h1>

    <form method="post"
          action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

        <input type="hidden"
               name="action"
               value="courier_save_shipment">

        <input type="hidden"
               name="id"
               value="<?php echo esc_attr($shipment->id ?? 0); ?>">

        <?php wp_nonce_field('courier_save_shipment'); ?>

        <table class="form-table">

            <tr>
                <th>
                    <label>Tracking</label>
                </th>
                <td>

                    <input
                        type="text"
                        name="tracking_number"
                        class="regular-text"
                        value="<?php echo esc_attr($shipment->tracking_number ?? ''); ?>"
                    >

                    <p class="description">
                        Déjelo vacío para generar uno automáticamente.
                    </p>

                </td>
            </tr>

            <tr>
                <th>
                    <label>Remitente</label>
                </th>
                <td>

                    <select name="sender_id" required>

                        <option value="">
                            Seleccione un cliente
                        </option>

                        <?php foreach ($clients as $client) : ?>

                            <?php
                            $selected =
                                (($shipment->sender_id ?? 0) == $client->id)
                                ? 'selected'
                                : '';
                            ?>

                            <option
                                value="<?php echo esc_attr($client->id); ?>"
                                <?php echo $selected; ?>
                            >
                                <?php
                                echo esc_html(
                                    trim(
                                        $client->first_name .
                                        ' ' .
                                        $client->last_name
                                    )
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </td>
            </tr>

            <tr>
                <th>
                    <label>Destinatario</label>
                </th>
                <td>

                    <select name="receiver_id" required>

                        <option value="">
                            Seleccione un cliente
                        </option>

                        <?php foreach ($clients as $client) : ?>

                            <?php
                            $selected =
                                (($shipment->receiver_id ?? 0) == $client->id)
                                ? 'selected'
                                : '';
                            ?>

                            <option
                                value="<?php echo esc_attr($client->id); ?>"
                                <?php echo $selected; ?>
                            >
                                <?php
                                echo esc_html(
                                    trim(
                                        $client->first_name .
                                        ' ' .
                                        $client->last_name
                                    )
                                );
                                ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </td>
            </tr>

            <tr>
                <th>
                    <label>Peso (kg)</label>
                </th>
                <td>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="weight"
                        value="<?php echo esc_attr($shipment->weight ?? 0); ?>"
                    >

                </td>
            </tr>

            <tr>
                <th>
                    <label>Estado</label>
                </th>
                <td>

                    <select name="status_id">

                        <?php foreach ($statuses as $status) : ?>

                            <?php
                            $selected =
                                (($shipment->current_status_id ?? 1) == $status->id)
                                ? 'selected'
                                : '';
                            ?>

                            <option
                                value="<?php echo esc_attr($status->id); ?>"
                                <?php echo $selected; ?>
                            >
                                <?php echo esc_html($status->name); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </td>
            </tr>

            <tr>
                <th>
                    <label>Descripción del paquete</label>
                </th>
                <td>

                    <textarea
                        name="package_description"
                        rows="5"
                        cols="60"
                    ><?php echo esc_textarea($shipment->package_description ?? ''); ?></textarea>

                </td>
            </tr>

        </table>

        <?php submit_button(
            $is_edit
                ? 'Actualizar Envío'
                : 'Guardar Envío'
        ); ?>

        <a href="<?php echo esc_url(
            admin_url('admin.php?page=courier-shipments')
        ); ?>"
           class="button">

            Cancelar

        </a>

    </form>

</div>