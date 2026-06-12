<?php if (!defined('ABSPATH')) exit; ?>

<?php $is_edit = !empty($client); ?>

<div class="wrap">

    <h1><?php echo $is_edit ? 'Editar Cliente' : 'Nuevo Cliente'; ?></h1>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">

        <input type="hidden" name="action" value="courier_save_client">

        <input type="hidden" name="id" value="<?php echo esc_attr($client->id ?? 0); ?>">

        <table class="form-table">

            <tr>
                <th>Nombre</th>
                <td>
                    <input type="text" name="first_name"
                           value="<?php echo esc_attr($client->first_name ?? ''); ?>" required>
                </td>
            </tr>

            <tr>
                <th>Apellido</th>
                <td>
                    <input type="text" name="last_name"
                           value="<?php echo esc_attr($client->last_name ?? ''); ?>">
                </td>
            </tr>

            <tr>
                <th>Email</th>
                <td>
                    <input type="email" name="email"
                           value="<?php echo esc_attr($client->email ?? ''); ?>">
                </td>
            </tr>

            <tr>
                <th>Teléfono</th>
                <td>
                    <input type="text" name="phone"
                           value="<?php echo esc_attr($client->phone ?? ''); ?>">
                </td>
            </tr>

            <tr>
                <th>Tipo</th>
                <td>
                    <select name="client_type">
                        <option value="individual"
                            <?php selected($client->client_type ?? '', 'individual'); ?>>
                            Individual
                        </option>

                        <option value="company"
                            <?php selected($client->client_type ?? '', 'company'); ?>>
                            Empresa
                        </option>
                    </select>
                </td>
            </tr>

        </table>

        <?php submit_button($is_edit ? 'Actualizar Cliente' : 'Guardar Cliente'); ?>

    </form>

</div>