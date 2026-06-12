<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$shipments = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT
            s.tracking_number,
            s.weight,
            s.created_at,
            st.name AS status_name,
            st.color AS status_color

         FROM {$wpdb->prefix}courier_shipments s

         LEFT JOIN {$wpdb->prefix}courier_statuses st
            ON s.current_status_id = st.id

         WHERE s.sender_id=%d
            OR s.receiver_id=%d

         ORDER BY s.id DESC",
        $client->id,
        $client->id
    )
);

$notes = [];

$notes_table = $wpdb->prefix . 'courier_client_notes';

if ($wpdb->get_var("SHOW TABLES LIKE '{$notes_table}'")) {

    $notes = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT *
             FROM {$notes_table}
             WHERE client_id=%d
             ORDER BY created_at DESC",
            $client->id
        )
    );
}

?>

<div class="wrap">

<h1>
Cliente #<?php echo esc_html($client->id); ?>
</h1>

<?php

$section = sanitize_text_field(
    $_GET['section'] ?? 'info'
);

?>

<h2 class="nav-tab-wrapper" style="margin-top:20px;">

    <a href="<?php echo admin_url(
        'admin.php?page=courier-clients&action=view&id=' .
        $client->id .
        '&section=info'
    ); ?>"
    class="nav-tab <?php echo ($section === 'info') ? 'nav-tab-active' : ''; ?>">
        Información
        
           
    </a>

    <a href="<?php echo admin_url(
        'admin.php?page=courier-clients&action=view&id=' .
        $client->id .
        '&section=shipments'
    ); ?>"
    class="nav-tab <?php echo ($section === 'shipments') ? 'nav-tab-active' : ''; ?>">
        Envíos
       
    </a>

    <a href="<?php echo admin_url(
        'admin.php?page=courier-clients&action=view&id=' .
        $client->id .
        '&section=tracking'
    ); ?>"
    class="nav-tab <?php echo ($section === 'tracking') ? 'nav-tab-active' : ''; ?>">
        Ver Tracking
       
    </a>

    <a href="<?php echo admin_url(
        'admin.php?page=courier-shipments&action=new&client_id=' .
        $client->id
    ); ?>"
    class="nav-tab">
        Nuevo Envío
    </a>

    <a href="<?php echo admin_url(
        'admin.php?page=courier-clients&action=view&id=' .
        $client->id .
        '&section=notes'
    ); ?>"
    class="nav-tab <?php echo ($section === 'notes') ? 'nav-tab-active' : ''; ?>">
        Notas
    </a>

</h2>

<?php if ($section === 'info') : ?>
    <?php if ($section === 'shipments') : ?>
<h2>Información General</h2>

<table class="form-table">

<tr>
<th>Nombre</th>
<td>
<?php
echo esc_html(
    trim(
        $client->first_name .
        ' ' .
        $client->last_name
    )
);
?>
</td>
</tr>

<tr>
<th>Email</th>
<td><?php echo esc_html($client->email); ?></td>
</tr>

<tr>
<th>Teléfono</th>
<td><?php echo esc_html($client->phone); ?></td>
</tr>

<tr>
<th>Tipo</th>
<td><?php echo esc_html($client->client_type); ?></td>
</tr>

</table>

<hr> Tracking
</a>
<h2>Pedidos / Envíos</h2>

<table class="wp-list-table widefat striped">

<thead>
<tr>
    <th>Tracking</th>
    <th>Estado</th>
    <th>Acción</th>
</tr>
</thead>

<tbody>

<?php foreach ($shipments as $shipment) : ?>

<tr>

<td>
    <?php echo esc_html($shipment->tracking_number); ?>
</td>

<td>
    <?php echo esc_html($shipment->status_name); ?>
</td>

<td>

    <a class="button button-small"
       href="<?php echo admin_url(
           'admin.php?page=courier-tracking&shipment_id=' .
           $shipment->id
       ); ?>">

       Ver Tracking

    </a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>

<form method="post"
      action="<?php echo admin_url('admin-post.php'); ?>">

<input type="hidden"
    name="action"
    value="courier_add_client_note">

<input type="hidden"
    name="client_id"
    value="<?php echo esc_attr($client->id); ?>">

<?php wp_nonce_field(
    'courier_add_client_note'
); ?>

<textarea
    name="note"
    rows="5"
    style="width:100%;"
    placeholder="Agregar nota interna..."
></textarea>

<p>

<button class="button button-primary">
Guardar Nota
</button>

<a href="<?php echo admin_url(
 'admin.php?page=courier-clients'
); ?>"
class="button">
Volver </a>

</p>

</form>

</div>
