<?php
/**
 * Services Admin Page
 *
 * @package RamboeckIT\ServiceConfigurator
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'rsc_services';

// Handle form submissions
if (isset($_POST['rsc_action'])) {
    check_admin_referer('rsc_service_action', 'rsc_service_nonce');

    $action = sanitize_text_field($_POST['rsc_action']);

    if ($action === 'add' || $action === 'edit') {
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
            'tooltip' => sanitize_textarea_field($_POST['tooltip']),
            'setup_price' => floatval($_POST['setup_price']),
            'monthly_price' => floatval($_POST['monthly_price']),
            'sort_order' => intval($_POST['sort_order']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'recommended_for' => sanitize_text_field($_POST['recommended_for'])
        );

        if ($action === 'add') {
            $wpdb->insert($table, $data);
            echo '<div class="notice notice-success"><p>Service erfolgreich hinzugefügt!</p></div>';
        } else {
            $id = intval($_POST['service_id']);
            $wpdb->update($table, $data, array('id' => $id));
            echo '<div class="notice notice-success"><p>Service erfolgreich aktualisiert!</p></div>';
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['service_id']);
        $wpdb->delete($table, array('id' => $id));
        echo '<div class="notice notice-success"><p>Service erfolgreich gelöscht!</p></div>';
    }
}

// Get service for editing
$editing_service = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $editing_service = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $edit_id));
}

// Get all services
$services = $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC");
?>

<div class="wrap">
    <h1><?php _e('Services verwalten', 'ramboeck-configurator'); ?></h1>

    <div class="rsc-admin-grid">
        <!-- Service Form -->
        <div class="rsc-admin-card">
            <h2><?php echo $editing_service ? __('Service bearbeiten', 'ramboeck-configurator') : __('Neuer Service', 'ramboeck-configurator'); ?></h2>

            <form method="post" action="">
                <?php wp_nonce_field('rsc_service_action', 'rsc_service_nonce'); ?>
                <input type="hidden" name="rsc_action" value="<?php echo $editing_service ? 'edit' : 'add'; ?>">
                <?php if ($editing_service): ?>
                    <input type="hidden" name="service_id" value="<?php echo esc_attr($editing_service->id); ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th><label for="name"><?php _e('Service Name', 'ramboeck-configurator'); ?> *</label></th>
                        <td><input type="text" id="name" name="name" value="<?php echo esc_attr($editing_service->name ?? ''); ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="description"><?php _e('Beschreibung', 'ramboeck-configurator'); ?> *</label></th>
                        <td><textarea id="description" name="description" rows="3" class="large-text" required><?php echo esc_textarea($editing_service->description ?? ''); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="tooltip"><?php _e('Tooltip / Details', 'ramboeck-configurator'); ?></label></th>
                        <td><textarea id="tooltip" name="tooltip" rows="2" class="large-text"><?php echo esc_textarea($editing_service->tooltip ?? ''); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="setup_price"><?php _e('Einrichtungskosten (EUR)', 'ramboeck-configurator'); ?></label></th>
                        <td><input type="number" id="setup_price" name="setup_price" value="<?php echo esc_attr($editing_service->setup_price ?? '0.00'); ?>" step="0.01" min="0" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="monthly_price"><?php _e('Monatliche Kosten (EUR)', 'ramboeck-configurator'); ?></label></th>
                        <td><input type="number" id="monthly_price" name="monthly_price" value="<?php echo esc_attr($editing_service->monthly_price ?? '0.00'); ?>" step="0.01" min="0" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="recommended_for"><?php _e('Empfohlen für (Branchen)', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <input type="text" id="recommended_for" name="recommended_for" value="<?php echo esc_attr($editing_service->recommended_for ?? 'all'); ?>" class="regular-text">
                            <p class="description"><?php _e('Komma-getrennt: healthcare,legal,accounting oder "all" für alle Branchen', 'ramboeck-configurator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="sort_order"><?php _e('Sortierreihenfolge', 'ramboeck-configurator'); ?></label></th>
                        <td><input type="number" id="sort_order" name="sort_order" value="<?php echo esc_attr($editing_service->sort_order ?? '0'); ?>" class="small-text"></td>
                    </tr>
                    <tr>
                        <th><label for="is_active"><?php _e('Status', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <label>
                                <input type="checkbox" id="is_active" name="is_active" value="1" <?php checked($editing_service->is_active ?? 1, 1); ?>>
                                <?php _e('Aktiv', 'ramboeck-configurator'); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php echo $editing_service ? __('Service aktualisieren', 'ramboeck-configurator') : __('Service hinzufügen', 'ramboeck-configurator'); ?>
                    </button>
                    <?php if ($editing_service): ?>
                        <a href="?page=ramboeck-configurator-services" class="button"><?php _e('Abbrechen', 'ramboeck-configurator'); ?></a>
                    <?php endif; ?>
                </p>
            </form>
        </div>

        <!-- Services List -->
        <div class="rsc-admin-card">
            <h2><?php _e('Vorhandene Services', 'ramboeck-configurator'); ?></h2>

            <?php if (empty($services)): ?>
                <p><?php _e('Noch keine Services vorhanden.', 'ramboeck-configurator'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Einrichtung', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Monatlich', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Status', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Aktionen', 'ramboeck-configurator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($service->name); ?></strong>
                                    <br><small><?php echo esc_html($service->description); ?></small>
                                </td>
                                <td><?php echo number_format($service->setup_price, 2, ',', '.'); ?> EUR</td>
                                <td><?php echo number_format($service->monthly_price, 2, ',', '.'); ?> EUR</td>
                                <td>
                                    <?php if ($service->is_active): ?>
                                        <span style="color: green;">● <?php _e('Aktiv', 'ramboeck-configurator'); ?></span>
                                    <?php else: ?>
                                        <span style="color: red;">● <?php _e('Inaktiv', 'ramboeck-configurator'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?page=ramboeck-configurator-services&edit=<?php echo $service->id; ?>" class="button button-small">
                                        <?php _e('Bearbeiten', 'ramboeck-configurator'); ?>
                                    </a>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('<?php esc_attr_e('Wirklich löschen?', 'ramboeck-configurator'); ?>');">
                                        <?php wp_nonce_field('rsc_service_action', 'rsc_service_nonce'); ?>
                                        <input type="hidden" name="rsc_action" value="delete">
                                        <input type="hidden" name="service_id" value="<?php echo $service->id; ?>">
                                        <button type="submit" class="button button-small button-link-delete">
                                            <?php _e('Löschen', 'ramboeck-configurator'); ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.rsc-admin-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 20px;
}
.rsc-admin-card {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
@media (max-width: 782px) {
    .rsc-admin-grid {
        grid-template-columns: 1fr;
    }
}
</style>
