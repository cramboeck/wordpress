<?php
/**
 * Industries Admin Page
 *
 * @package RamboeckIT\ServiceConfigurator
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$presets_table = $wpdb->prefix . 'rsc_industry_presets';
$services_table = $wpdb->prefix . 'rsc_services';

// Handle form submissions
if (isset($_POST['rsc_action'])) {
    check_admin_referer('rsc_industry_action', 'rsc_industry_nonce');

    $action = sanitize_text_field($_POST['rsc_action']);

    if ($action === 'add' || $action === 'edit') {
        $data = array(
            'industry_key' => sanitize_key($_POST['industry_key']),
            'industry_name' => sanitize_text_field($_POST['industry_name']),
            'description' => sanitize_textarea_field($_POST['description']),
            'recommended_services' => sanitize_text_field($_POST['recommended_services']),
            'icon' => sanitize_text_field($_POST['icon']),
            'sort_order' => intval($_POST['sort_order'])
        );

        if ($action === 'add') {
            $wpdb->insert($presets_table, $data);
            echo '<div class="notice notice-success"><p>Branche erfolgreich hinzugefügt!</p></div>';
        } else {
            $id = intval($_POST['industry_id']);
            $wpdb->update($presets_table, $data, array('id' => $id));
            echo '<div class="notice notice-success"><p>Branche erfolgreich aktualisiert!</p></div>';
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['industry_id']);
        $wpdb->delete($presets_table, array('id' => $id));
        echo '<div class="notice notice-success"><p>Branche erfolgreich gelöscht!</p></div>';
    }
}

// Get industry for editing
$editing_industry = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $editing_industry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $presets_table WHERE id = %d", $edit_id));
}

// Get all industries and services
$industries = $wpdb->get_results("SELECT * FROM $presets_table ORDER BY sort_order ASC");
$services = $wpdb->get_results("SELECT id, name FROM $services_table ORDER BY sort_order ASC");
?>

<div class="wrap">
    <h1><?php _e('Branchen & Presets verwalten', 'ramboeck-configurator'); ?></h1>

    <div class="rsc-admin-grid">
        <!-- Industry Form -->
        <div class="rsc-admin-card">
            <h2><?php echo $editing_industry ? __('Branche bearbeiten', 'ramboeck-configurator') : __('Neue Branche', 'ramboeck-configurator'); ?></h2>

            <form method="post" action="">
                <?php wp_nonce_field('rsc_industry_action', 'rsc_industry_nonce'); ?>
                <input type="hidden" name="rsc_action" value="<?php echo $editing_industry ? 'edit' : 'add'; ?>">
                <?php if ($editing_industry): ?>
                    <input type="hidden" name="industry_id" value="<?php echo esc_attr($editing_industry->id); ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th><label for="industry_key"><?php _e('Schlüssel (Key)', 'ramboeck-configurator'); ?> *</label></th>
                        <td>
                            <input type="text" id="industry_key" name="industry_key" value="<?php echo esc_attr($editing_industry->industry_key ?? ''); ?>" class="regular-text" required pattern="[a-z0-9_-]+" <?php echo $editing_industry ? 'readonly' : ''; ?>>
                            <p class="description"><?php _e('Nur Kleinbuchstaben, Zahlen, Bindestrich und Unterstrich. Kann nach Erstellung nicht geändert werden.', 'ramboeck-configurator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="industry_name"><?php _e('Anzeigename', 'ramboeck-configurator'); ?> *</label></th>
                        <td><input type="text" id="industry_name" name="industry_name" value="<?php echo esc_attr($editing_industry->industry_name ?? ''); ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="description"><?php _e('Beschreibung', 'ramboeck-configurator'); ?></label></th>
                        <td><textarea id="description" name="description" rows="2" class="large-text"><?php echo esc_textarea($editing_industry->description ?? ''); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="icon"><?php _e('Icon (optional)', 'ramboeck-configurator'); ?></label></th>
                        <td>
                            <input type="text" id="icon" name="icon" value="<?php echo esc_attr($editing_industry->icon ?? 'business'); ?>" class="regular-text">
                            <p class="description"><?php _e('Icon-Name für die Frontend-Darstellung', 'ramboeck-configurator'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="recommended_services"><?php _e('Empfohlene Services', 'ramboeck-configurator'); ?> *</label></th>
                        <td>
                            <input type="text" id="recommended_services" name="recommended_services" value="<?php echo esc_attr($editing_industry->recommended_services ?? ''); ?>" class="large-text" required>
                            <p class="description"><?php _e('Komma-getrennte Service-IDs (z.B. 1,2,3,5,7)', 'ramboeck-configurator'); ?></p>

                            <div style="margin-top: 10px; max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                                <strong><?php _e('Verfügbare Services:', 'ramboeck-configurator'); ?></strong><br>
                                <?php foreach ($services as $service): ?>
                                    <label style="display: block; margin: 5px 0;">
                                        <code><?php echo $service->id; ?></code> - <?php echo esc_html($service->name); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="sort_order"><?php _e('Sortierreihenfolge', 'ramboeck-configurator'); ?></label></th>
                        <td><input type="number" id="sort_order" name="sort_order" value="<?php echo esc_attr($editing_industry->sort_order ?? '0'); ?>" class="small-text"></td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php echo $editing_industry ? __('Branche aktualisieren', 'ramboeck-configurator') : __('Branche hinzufügen', 'ramboeck-configurator'); ?>
                    </button>
                    <?php if ($editing_industry): ?>
                        <a href="?page=ramboeck-configurator-industries" class="button"><?php _e('Abbrechen', 'ramboeck-configurator'); ?></a>
                    <?php endif; ?>
                </p>
            </form>
        </div>

        <!-- Industries List -->
        <div class="rsc-admin-card">
            <h2><?php _e('Vorhandene Branchen', 'ramboeck-configurator'); ?></h2>

            <?php if (empty($industries)): ?>
                <p><?php _e('Noch keine Branchen definiert.', 'ramboeck-configurator'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Name', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Schlüssel', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Services', 'ramboeck-configurator'); ?></th>
                            <th><?php _e('Aktionen', 'ramboeck-configurator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($industries as $industry): ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($industry->industry_name); ?></strong>
                                    <?php if ($industry->description): ?>
                                        <br><small><?php echo esc_html($industry->description); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo esc_html($industry->industry_key); ?></code></td>
                                <td>
                                    <?php
                                    $service_ids = array_filter(array_map('intval', explode(',', $industry->recommended_services)));
                                    if (!empty($service_ids)) {
                                        $ids_str = implode(',', $service_ids);
                                        $preset_services = $wpdb->get_col("SELECT name FROM $services_table WHERE id IN ($ids_str) ORDER BY sort_order");
                                        echo '<small>' . implode(', ', array_slice($preset_services, 0, 3));
                                        if (count($preset_services) > 3) {
                                            echo ' <em>(+' . (count($preset_services) - 3) . ' weitere)</em>';
                                        }
                                        echo '</small>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="?page=ramboeck-configurator-industries&edit=<?php echo $industry->id; ?>" class="button button-small">
                                        <?php _e('Bearbeiten', 'ramboeck-configurator'); ?>
                                    </a>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('<?php esc_attr_e('Wirklich löschen?', 'ramboeck-configurator'); ?>');">
                                        <?php wp_nonce_field('rsc_industry_action', 'rsc_industry_nonce'); ?>
                                        <input type="hidden" name="rsc_action" value="delete">
                                        <input type="hidden" name="industry_id" value="<?php echo $industry->id; ?>">
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

    <div class="rsc-admin-card" style="margin-top: 20px;">
        <h2><?php _e('Branchen-Service Matrix', 'ramboeck-configurator'); ?></h2>
        <p><?php _e('Übersicht welche Services für welche Branchen empfohlen werden:', 'ramboeck-configurator'); ?></p>

        <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th><?php _e('Service', 'ramboeck-configurator'); ?></th>
                    <?php foreach ($industries as $industry): ?>
                        <th style="text-align: center; font-size: 11px;">
                            <?php echo esc_html($industry->industry_name); ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><strong><?php echo esc_html($service->name); ?></strong></td>
                        <?php foreach ($industries as $industry): ?>
                            <?php
                            $recommended_ids = array_map('intval', explode(',', $industry->recommended_services));
                            $is_recommended = in_array($service->id, $recommended_ids);
                            ?>
                            <td style="text-align: center;">
                                <?php if ($is_recommended): ?>
                                    <span style="color: green; font-size: 20px;">✓</span>
                                <?php else: ?>
                                    <span style="color: #ddd;">—</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
