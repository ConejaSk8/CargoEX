<?php

if (!defined('ABSPATH')) {
    exit;
}

class Courier_Installer {

    public static function install() {

        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        $clients_table          = $wpdb->prefix . 'courier_clients';
        $addresses_table        = $wpdb->prefix . 'courier_addresses';
        $shipments_table        = $wpdb->prefix . 'courier_shipments';
        $statuses_table         = $wpdb->prefix . 'courier_statuses';
        $tracking_table         = $wpdb->prefix . 'courier_tracking';
        $drivers_table          = $wpdb->prefix . 'courier_drivers';
        $routes_table           = $wpdb->prefix . 'courier_routes';
        $route_shipments_table  = $wpdb->prefix . 'courier_route_shipments';
        $proofs_table           = $wpdb->prefix . 'courier_proofs';

        $sql = [];

$sql[] = "CREATE TABLE {$wpdb->prefix}courier_client_notes (

    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    client_id BIGINT UNSIGNED NOT NULL,

    note TEXT NOT NULL,

    created_by BIGINT UNSIGNED NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY client_id (client_id)

) $charset_collate;";
        
        // AQUÍ VAN TODAS LAS SENTENCIAS CREATE TABLE QUE YA TIENES

        foreach ($sql as $query) {
            dbDelta($query);
        }

        // Crear estados por defecto
        self::insert_default_statuses();

        update_option('courier_plugin_version', '1.2.0');
    }



    /**
     * Inserta estados iniciales del sistema.
     */
    private static function insert_default_statuses() {

        global $wpdb;

        $table = $wpdb->prefix . 'courier_statuses';

        $statuses = [

            [
                'name'  => 'Recibido',
                'code'  => 'received',
                'color' => '#3498db'
            ],

            [
                'name'  => 'En Bodega',
                'code'  => 'warehouse',
                'color' => '#f39c12'
            ],

            [
                'name'  => 'En Tránsito',
                'code'  => 'transit',
                'color' => '#9b59b6'
            ],

            [
                'name'  => 'Aduana',
                'code'  => 'customs',
                'color' => '#e67e22'
            ],

            [
                'name'  => 'Listo para Entrega',
                'code'  => 'out_delivery',
                'color' => '#16a085'
            ],

            [
                'name'  => 'Entregado',
                'code'  => 'delivered',
                'color' => '#27ae60'
            ],

            [
                'name'  => 'Cancelado',
                'code'  => 'cancelled',
                'color' => '#c0392b'
            ]

        ];

        foreach ($statuses as $status) {

            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM {$table} WHERE code = %s",
                    $status['code']
                )
            );

            if (!$exists) {

                $wpdb->insert(
                    $table,
                    [
                        'name'  => $status['name'],
                        'code'  => $status['code'],
                        'color' => $status['color']
                    ],
                    [
                        '%s',
                        '%s',
                        '%s'
                    ]
                );
            }
        }
    }
}
