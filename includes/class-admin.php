<?php

if (!defined('ABSPATH')) {
    exit;
}

class Courier_Admin {

    public static function init() {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_post_courier_save_client', [self::class, 'save_client']);
        add_action('admin_post_courier_delete_client', [self::class, 'delete_client']);
        add_action('admin_post_courier_clients_bulk', [self::class, 'clients_bulk_action']);
        add_action('admin_post_courier_save_shipment', [self::class,'save_shipment']);
        add_action('admin_post_courier_delete_shipment',[self::class,'delete_shipment']);
        add_action('admin_post_courier_add_client_note', [self::class,'add_client_note']);
        
    }

    public static function register_menu() {

        add_menu_page(
            'Cargo EX',                 // Título página
            'Cargo EX',                 // Texto menú
            'manage_options',              // Permiso
            'cargoex',                 // Slug
            [self::class, 'dashboard_page'],
            'dashicons-location-alt',
            25
        );

        add_submenu_page(
            'cargoex',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'cargoex',
            [self::class, 'dashboard_page']
        );

        add_submenu_page(
            'cargoex',
            'Envíos',
            'Envíos',
            'manage_options',
            'courier-shipments',
            [self::class, 'shipments_page']
        );

        add_submenu_page(
            'cargoex',
            'Clientes',
            'Clientes',
            'manage_options',
            'courier-clients',
            [self::class, 'clients_page']
        );

        add_submenu_page(
            'cargoex',
            'Repartidores',
            'Repartidores',
            'manage_options',
            'courier-drivers',
            [self::class, 'drivers_page']
        );

        add_submenu_page(
            'cargoex',
            'Rutas',
            'Rutas',
            'manage_options',
            'courier-routes',
            [self::class, 'routes_page']
        );
    }


     /**
     * Cargador de vistas
     */
    private static function load_view($view, $data = [])
    {
        extract($data);

        $file = COURIER_PLUGIN_PATH . 'admin/views/' . $view . '.php';

        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="notice notice-error"><p>Vista no encontrada: ' . esc_html($view) . '</p></div>';
        }
    }

    /**
     * Dashboard
     */
    public static function dashboard_page()
    {
        self::load_view('dashboard');
    }


    public static function shipments_page()
{
    global $wpdb;

    $table = $wpdb->prefix .
             'courier_shipments';

    $action = $_GET['action'] ?? '';

    $id = intval(
        $_GET['id'] ?? 0
    );

    if ($action === 'new') {

        $shipment = (object)[
            'id' => 0,
            'tracking_number' => '',
            'sender_id' => 0,
            'receiver_id' => 0,
            'weight' => 0,
            'package_description' => '',
            'current_status_id' => 1
        ];

        self::load_view(
            'shipments-form',
            [
                'shipment' => $shipment
            ]
        );

        return;
    }

    if ($action === 'edit' && $id > 0) {

        $shipment = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT *
                 FROM $table
                 WHERE id=%d",
                $id
            )
        );

        self::load_view(
            'shipments-form',
            [
                'shipment' => $shipment
            ]
        );

        return;
    }

    $shipments = $wpdb->get_results(
        "SELECT *
         FROM $table
         ORDER BY id DESC"
    );

    self::load_view(
        'shipments',
        [
            'shipments' => $shipments
        ]
    );
}

    // PAGINA DE CLIENTES
    public static function clients_page()
{
    global $wpdb;

    $table = $wpdb->prefix . 'courier_clients';



    $action = $_GET['action'] ?? '';
    $id     = intval($_GET['id'] ?? 0);

    /* 🆕 NUEVO CLIENTE */
    if ($action === 'new') {

        // cliente vacío para formulario
        $client = (object)[
            'id' => 0,
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'client_type' => 'individual'
        ];

        self::load_view('clients-form', [
            'client' => $client
        ]);

        return;
    }

    if ($action === 'view' && $id > 0) {

        global $wpdb;

        $client = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT *
                 FROM {$wpdb->prefix}courier_clients
                 WHERE id=%d",
                $id
            )
        );

        self::load_view(
            'client-view',
            [
                'client' => $client
            ]
        );

        return;
        }

    /* ✏ EDITAR */
    if ($action === 'edit' && $id > 0) {

        $client = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id)
        );

        self::load_view('clients-form', [
            'client' => $client
        ]);

        return;
    }

    /* 📋 LISTA FILTRADA (AQUÍ USAMOS LA FUNCIÓN) */
    $clients = self::get_filtered_clients();

    self::load_view('clients', [
        'clients' => $clients
    ]);
}

    /** 
     GUARDA Y EDITAR 
     **/
    public static function save_client()
        {
        if (!current_user_can('manage_options')) {
            check_admin_referer('courier_save_client');
            wp_die('No autorizado');
        }


        global $wpdb;

        $table = $wpdb->prefix . 'courier_clients';

        $id = intval($_POST['id'] ?? 0);

         $data = [
        'first_name'  => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name'   => sanitize_text_field($_POST['last_name'] ?? ''),
        'email'       => sanitize_email($_POST['email'] ?? ''),
        'phone'       => sanitize_text_field($_POST['phone'] ?? ''),
        'client_type' => sanitize_text_field($_POST['client_type'] ?? 'individual'),
    ];

        if ($id > 0) {
            // UPDATE
            $wpdb->update($table, $data, ['id' => $id]);
            } else {
            // INSERT
            $data['created_at'] = current_time('mysql');
            $wpdb->insert($table, $data);
        }

         
         wp_redirect(admin_url('admin.php?page=courier-clients&saved=1'));
        exit;
    }

    public static function delete_client()
    {
        if (!current_user_can('manage_options')) {
            check_admin_referer('courier_delete_client');
            wp_die('No autorizado');
        }

        global $wpdb;

        $table = $wpdb->prefix . 'courier_clients';

        $id = intval($_GET['id'] ?? 0);

        if ($id > 0) {
            $wpdb->delete($table, ['id' => $id]);
        }

        wp_redirect(admin_url('admin.php?page=courier-clients&deleted=id'));
        exit;
    }

    public static function clients_bulk_action()
        {
            if (!current_user_can('manage_options')) {
                wp_die('No autorizado');
            }

            global $wpdb;

            $table = $wpdb->prefix . 'courier_clients';

            $action = $_POST['bulk_action'] ?? '';
            $ids    = $_POST['client_ids'] ?? [];

            if (empty($ids)) {
                wp_redirect(admin_url('admin.php?page=courier-clients'));
                exit;
            }

            $ids = array_map('intval', $ids);

            // 🗑 ELIMINAR
            if ($action === 'delete') {

                foreach ($ids as $id) {
                    $wpdb->delete($table, ['id' => $id]);
                }

                wp_redirect(admin_url('admin.php?page=courier-clients&deleted=1'));
                exit;
            }

            // ✏ EDITAR (solo 1 cliente permitido)
            if ($action === 'edit' && count($ids) === 1) {

                $id = $ids[0];

                wp_redirect(admin_url('admin.php?page=courier-clients&action=edit&id=' . $id));
                exit;
            }

            wp_redirect(admin_url('admin.php?page=courier-clients'));
            exit;
        }

        public static function get_filtered_clients()
        {
            global $wpdb;

            $table = $wpdb->prefix . 'courier_clients';

            $where = "WHERE 1=1";

            /* 🔍 SEARCH */
            if (!empty($_GET['s'])) {

                $search = sanitize_text_field($_GET['s']);

                $where .= $wpdb->prepare(
                    " AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s)",
                    '%' . $wpdb->esc_like($search) . '%',
                    '%' . $wpdb->esc_like($search) . '%',
                    '%' . $wpdb->esc_like($search) . '%'
                );
            }

            /* 🏷 FILTER TYPE */
            if (isset($_GET['type']) && $_GET['type'] !== '') {

                $type = sanitize_text_field($_GET['type']);

                $where .= $wpdb->prepare(
                    " AND client_type = %s",
                    $type
                );
            }

            $sql = "SELECT * FROM $table $where ORDER BY id DESC";

            return $wpdb->get_results($sql);
        }


        //TRAKING
        private static function add_tracking_event(
            $shipment_id,
            $status_id,
            $note=''
        )
        {
            global $wpdb;

            $wpdb->insert(
                $wpdb->prefix.'courier_tracking',
                [
                    'shipment_id'=>$shipment_id,
                    'status_id'=>$status_id,
                    'notes'=>$note,
                    'created_by'=>get_current_user_id(),
                    'created_at'=>current_time('mysql')
                ]
            );
        }
        //GENERAR TRAKING
        private static function generate_tracking()
{
    global $wpdb;

    $table = $wpdb->prefix .
             'courier_shipments';

    do {

        $tracking =
            'CX' .
            date('Ymd') .
            strtoupper(
                wp_generate_password(
                    6,
                    false,
                    false
                )
            );

        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id
                 FROM $table
                 WHERE tracking_number=%s",
                $tracking
            )
        );

    } while ($exists);

    return $tracking;
}

        //SAVE SHIPMENTS
        public static function save_shipment()
        {
            if (!current_user_can('manage_options')) {
                wp_die('No autorizado');
            }

            check_admin_referer('courier_save_shipment');

            global $wpdb;

            $table_shipments = $wpdb->prefix . 'courier_shipments';

            $id = intval($_POST['id'] ?? 0);

            $status_id = intval($_POST['status_id'] ?? 1);

            $tracking = sanitize_text_field(
                $_POST['tracking_number'] ?? ''
            );

            if (empty($tracking)) {
                $tracking = self::generate_tracking();
            }

            $data = [

                'tracking_number'      => $tracking,
                'sender_id'            => intval($_POST['sender_id'] ?? 0),
                'receiver_id'          => intval($_POST['receiver_id'] ?? 0),
                'weight'               => floatval($_POST['weight'] ?? 0),
                'package_description'  => sanitize_textarea_field(
                    $_POST['package_description'] ?? ''
                ),
                'current_status_id'    => $status_id,
                'updated_at'           => current_time('mysql')
            ];

            if ($id > 0) {

                $wpdb->update(
                    $table_shipments,
                    $data,
                    ['id' => $id]
                );

            } else {

                $data['created_by'] = get_current_user_id();
                $data['created_at'] = current_time('mysql');

                $wpdb->insert(
                    $table_shipments,
                    $data
                );

                if ($wpdb->last_error) {

                    wp_die(
                        'Error guardando envío: ' .
                        $wpdb->last_error
                    );
                }

                $id = $wpdb->insert_id;
            }

            self::add_tracking_event(
                $id,
                $status_id,
                'Envío creado o actualizado'
            );

            wp_redirect(
                admin_url(
                    'admin.php?page=courier-shipments&saved=1'
                )
            );

            exit;
        }

        //DELETE SHIPMENT
        public static function delete_shipment()
    {
        if (!current_user_can('manage_options')) {
            wp_die('No autorizado');
        }

        check_admin_referer(
            'courier_delete_shipment'
        );

        global $wpdb;

        $table = $wpdb->prefix .
                 'courier_shipments';

        $id = intval($_GET['id'] ?? 0);

        if ($id > 0) {

            $wpdb->delete(
                $table,
                ['id' => $id]
            );
        }

        wp_redirect(
            admin_url(
                'admin.php?page=courier-shipments&deleted=1'
            )
        );

        exit;
    }

    public static function add_client_note()
    {
        check_admin_referer(
            'courier_add_client_note'
        );

        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix .
            'courier_client_notes',
            [
                'client_id' => intval($_POST['client_id']),
                'note' => sanitize_textarea_field($_POST['note']),
                'created_by' => get_current_user_id()
            ]
        );

        wp_redirect(
            wp_get_referer()
        );

        exit;
    }

    
}