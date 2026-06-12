<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$table_shipments = $wpdb->prefix . 'courier_shipments';
$table_clients   = $wpdb->prefix . 'courier_clients';
$table_statuses  = $wpdb->prefix . 'courier_statuses';

$shipments = $wpdb->get_results("
    SELECT

        s.*,

        CONCAT(sc.first_name,' ',sc.last_name) AS sender_name,

        CONCAT(rc.first_name,' ',rc.last_name) AS receiver_name,

        st.name AS status_name,
        st.color AS status_color

    FROM $table_shipments s

    LEFT JOIN $table_clients sc
        ON s.sender_id = sc.id

    LEFT JOIN $table_clients rc
        ON s.receiver_id = rc.id

    LEFT JOIN $table_statuses st
        ON s.current_status_id = st.id

    ORDER BY s.id DESC
");

?>

<div class="wrap">

```
<h1 class="wp-heading-inline">
    Envíos
</h1>

<a class="page-title-action"
   href="<?php echo admin_url('admin.php?page=courier-shipments&action=new'); ?>">
    Nuevo Envío
</a>

<hr class="wp-header-end">

<?php if (isset($_GET['saved'])) : ?>

    <div class="notice notice-success is-dismissible">
        <p>Envío guardado correctamente.</p>
    </div>

<?php endif; ?>

<?php if (isset($_GET['deleted'])) : ?>

    <div class="notice notice-success is-dismissible">
        <p>Envío eliminado correctamente.</p>
    </div>

<?php endif; ?>

<table class="wp-list-table widefat fixed striped">

    <thead>

        <tr>

            <th width="70">ID</th>

            <th>Tracking</th>

            <th>Remitente</th>

            <th>Destinatario</th>

            <th>Peso</th>

            <th>Estado</th>

            <th>Descripción</th>

            <th>Fecha</th>

            <th width="220">Acciones</th>

        </tr>

    </thead>

    <tbody>

    <?php if (!empty($shipments)) : ?>

        <?php foreach ($shipments as $shipment) : ?>

            <?php

            $delete_url = wp_nonce_url(
                admin_url(
                    'admin-post.php?action=courier_delete_shipment&id=' .
                    $shipment->id
                ),
                'courier_delete_shipment'
            );

            ?>

            <tr>

                <td>
                    #<?php echo intval($shipment->id); ?>
                </td>

                <td>
                    <strong>
                        <?php echo esc_html($shipment->tracking_number); ?>
                    </strong>
                </td>

                <td>
                    <?php echo esc_html($shipment->sender_name ?: 'N/D'); ?>
                </td>

                <td>
                    <?php echo esc_html($shipment->receiver_name ?: 'N/D'); ?>
                </td>

                <td>
                    <?php echo number_format($shipment->weight, 2); ?> kg
                </td>

                <td>

                    <span
                        style="
                            background:<?php echo esc_attr($shipment->status_color ?: '#777'); ?>;
                            color:#fff;
                            padding:4px 10px;
                            border-radius:20px;
                            font-size:12px;
                            font-weight:600;
                        ">

                        <?php echo esc_html($shipment->status_name ?: 'Sin estado'); ?>

                    </span>

                </td>

                <td>

                    <?php

                    echo esc_html(
                        wp_trim_words(
                            $shipment->package_description,
                            8
                        )
                    );

                    ?>

                </td>

                <td>

                    <?php

                    echo esc_html(
                        date(
                            'd/m/Y H:i',
                            strtotime($shipment->created_at)
                        )
                    );

                    ?>

                </td>

                <td>

                    <a href="<?php echo admin_url(
                        'admin.php?page=courier-shipments&action=edit&id=' .
                        $shipment->id
                    ); ?>">
                        Editar
                    </a>

                    |

                    <a href="<?php echo admin_url(
                        'admin.php?page=courier-tracking&shipment_id=' .
                        $shipment->id
                    ); ?>">
                        Tracking
                    </a>

                    |

                    <a href="<?php echo esc_url($delete_url); ?>"
                       onclick="return confirm('¿Eliminar este envío?');">
                        Eliminar
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    <?php else : ?>

        <tr>

            <td colspan="9">

                No hay envíos registrados.

            </td>

        </tr>

    <?php endif; ?>

    </tbody>

</table>
```

</div>
