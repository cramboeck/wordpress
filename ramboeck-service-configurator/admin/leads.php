<?php
/**
 * Leads Admin Page
 *
 * @package RamboeckIT\ServiceConfigurator
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'rsc_leads';

// Handle status updates
if (isset($_POST['rsc_update_status'])) {
    check_admin_referer('rsc_lead_action', 'rsc_lead_nonce');

    $lead_id = intval($_POST['lead_id']);
    $new_status = sanitize_text_field($_POST['status']);

    $wpdb->update($table, array('status' => $new_status), array('id' => $lead_id));
    echo '<div class="notice notice-success"><p>Status aktualisiert!</p></div>';
}

// Handle delete
if (isset($_POST['rsc_delete_lead'])) {
    check_admin_referer('rsc_lead_action', 'rsc_lead_nonce');

    $lead_id = intval($_POST['lead_id']);
    $wpdb->delete($table, array('id' => $lead_id));
    echo '<div class="notice notice-success"><p>Anfrage gelöscht!</p></div>';
}

// Handle notes update
if (isset($_POST['rsc_update_notes'])) {
    check_admin_referer('rsc_lead_action', 'rsc_lead_nonce');

    $lead_id = intval($_POST['lead_id']);
    $notes = sanitize_textarea_field($_POST['notes']);

    $wpdb->update($table, array('notes' => $notes), array('id' => $lead_id));
    echo '<div class="notice notice-success"><p>Notizen gespeichert!</p></div>';
}

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    check_admin_referer('rsc_export_leads');

    $leads = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=leads-' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Datum', 'Name', 'Email', 'Telefon', 'Firma', 'Branche', 'Mitarbeiter', 'Standorte', 'Einrichtung', 'Monatlich', 'Status'));

    foreach ($leads as $lead) {
        fputcsv($output, array(
            $lead->id,
            $lead->created_at,
            $lead->name,
            $lead->email,
            $lead->phone,
            $lead->company,
            $lead->industry,
            $lead->company_size,
            $lead->locations,
            number_format($lead->total_setup, 2, ',', '.'),
            number_format($lead->total_monthly, 2, ',', '.'),
            $lead->status
        ));
    }

    fclose($output);
    exit;
}

// Filtering
$where = array('1=1');
$filter_status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '';
$filter_industry = isset($_GET['filter_industry']) ? sanitize_text_field($_GET['filter_industry']) : '';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

if ($filter_status) {
    $where[] = $wpdb->prepare("status = %s", $filter_status);
}
if ($filter_industry) {
    $where[] = $wpdb->prepare("industry = %s", $filter_industry);
}
if ($search) {
    $where[] = $wpdb->prepare("(name LIKE %s OR email LIKE %s OR company LIKE %s)",
        '%' . $wpdb->esc_like($search) . '%',
        '%' . $wpdb->esc_like($search) . '%',
        '%' . $wpdb->esc_like($search) . '%'
    );
}

$where_clause = implode(' AND ', $where);
$leads = $wpdb->get_results("SELECT * FROM $table WHERE $where_clause ORDER BY created_at DESC");

// Get unique industries and statuses for filters
$industries = $wpdb->get_col("SELECT DISTINCT industry FROM $table WHERE industry != '' ORDER BY industry");
$statuses = array('new', 'contacted', 'quoted', 'won', 'lost');

// View single lead
$viewing_lead = null;
if (isset($_GET['view'])) {
    $view_id = intval($_GET['view']);
    $viewing_lead = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $view_id));
}
?>

<div class="wrap">
    <h1>
        <?php _e('Anfragen', 'ramboeck-configurator'); ?>
        <a href="<?php echo wp_nonce_url(add_query_arg('export', 'csv'), 'rsc_export_leads'); ?>" class="page-title-action">
            <?php _e('Als CSV exportieren', 'ramboeck-configurator'); ?>
        </a>
    </h1>

    <?php if ($viewing_lead): ?>
        <!-- Single Lead View -->
        <div class="rsc-lead-detail">
            <a href="?page=ramboeck-configurator" class="button">&larr; <?php _e('Zurück zur Übersicht', 'ramboeck-configurator'); ?></a>

            <div class="rsc-admin-card" style="margin-top: 20px;">
                <h2><?php _e('Anfrage Details', 'ramboeck-configurator'); ?> #<?php echo $viewing_lead->id; ?></h2>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <!-- Left Column -->
                    <div>
                        <h3><?php _e('Kontaktdaten', 'ramboeck-configurator'); ?></h3>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Name', 'ramboeck-configurator'); ?>:</th>
                                <td><strong><?php echo esc_html($viewing_lead->name); ?></strong></td>
                            </tr>
                            <tr>
                                <th><?php _e('Email', 'ramboeck-configurator'); ?>:</th>
                                <td><a href="mailto:<?php echo esc_attr($viewing_lead->email); ?>"><?php echo esc_html($viewing_lead->email); ?></a></td>
                            </tr>
                            <tr>
                                <th><?php _e('Telefon', 'ramboeck-configurator'); ?>:</th>
                                <td><?php echo esc_html($viewing_lead->phone); ?></td>
                            </tr>
                            <tr>
                                <th><?php _e('Firma', 'ramboeck-configurator'); ?>:</th>
                                <td><?php echo esc_html($viewing_lead->company); ?></td>
                            </tr>
                            <tr>
                                <th><?php _e('Branche', 'ramboeck-configurator'); ?>:</th>
                                <td><?php echo esc_html($viewing_lead->industry); ?></td>
                            </tr>
                            <tr>
                                <th><?php _e('Mitarbeiter', 'ramboeck-configurator'); ?>:</th>
                                <td><?php echo esc_html($viewing_lead->company_size); ?></td>
                            </tr>
                            <tr>
                                <th><?php _e('Standorte', 'ramboeck-configurator'); ?>:</th>
                                <td><?php echo esc_html($viewing_lead->locations); ?></td>
                            </tr>
                            <tr>
                                <th><?php _e('Datum', 'ramboeck-configurator'); ?>:</th>
                                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($viewing_lead->created_at)); ?></td>
                            </tr>
                        </table>

                        <h3><?php _e('Status', 'ramboeck-configurator'); ?></h3>
                        <form method="post">
                            <?php wp_nonce_field('rsc_lead_action', 'rsc_lead_nonce'); ?>
                            <input type="hidden" name="lead_id" value="<?php echo $viewing_lead->id; ?>">
                            <select name="status" class="regular-text">
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?php echo esc_attr($status); ?>" <?php selected($viewing_lead->status, $status); ?>>
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="rsc_update_status" class="button button-primary"><?php _e('Status aktualisieren', 'ramboeck-configurator'); ?></button>
                        </form>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <h3><?php _e('Gewählte Services', 'ramboeck-configurator'); ?></h3>
                        <?php
                        $services = json_decode($viewing_lead->configuration, true);
                        if (is_array($services) && !empty($services)):
                        ?>
                            <table class="wp-list-table widefat fixed striped">
                                <thead>
                                    <tr>
                                        <th><?php _e('Service', 'ramboeck-configurator'); ?></th>
                                        <th><?php _e('Einrichtung', 'ramboeck-configurator'); ?></th>
                                        <th><?php _e('Monatlich', 'ramboeck-configurator'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($services as $service): ?>
                                        <tr>
                                            <td><?php echo esc_html($service['name']); ?></td>
                                            <td><?php echo number_format($service['setup_price'], 2, ',', '.'); ?> EUR</td>
                                            <td><?php echo number_format($service['monthly_price'], 2, ',', '.'); ?> EUR</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th><?php _e('Gesamt', 'ramboeck-configurator'); ?></th>
                                        <th><strong><?php echo number_format($viewing_lead->total_setup, 2, ',', '.'); ?> EUR</strong></th>
                                        <th><strong><?php echo number_format($viewing_lead->total_monthly, 2, ',', '.'); ?> EUR</strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php else: ?>
                            <p><?php _e('Keine Services ausgewählt.', 'ramboeck-configurator'); ?></p>
                        <?php endif; ?>

                        <h3><?php _e('Notizen', 'ramboeck-configurator'); ?></h3>
                        <form method="post">
                            <?php wp_nonce_field('rsc_lead_action', 'rsc_lead_nonce'); ?>
                            <input type="hidden" name="lead_id" value="<?php echo $viewing_lead->id; ?>">
                            <textarea name="notes" rows="5" class="large-text"><?php echo esc_textarea($viewing_lead->notes); ?></textarea>
                            <p>
                                <button type="submit" name="rsc_update_notes" class="button button-primary"><?php _e('Notizen speichern', 'ramboeck-configurator'); ?></button>
                            </p>
                        </form>

                        <h3><?php _e('Aktionen', 'ramboeck-configurator'); ?></h3>
                        <form method="post" onsubmit="return confirm('<?php esc_attr_e('Anfrage wirklich löschen?', 'ramboeck-configurator'); ?>');">
                            <?php wp_nonce_field('rsc_lead_action', 'rsc_lead_nonce'); ?>
                            <input type="hidden" name="lead_id" value="<?php echo $viewing_lead->id; ?>">
                            <button type="submit" name="rsc_delete_lead" class="button button-link-delete"><?php _e('Anfrage löschen', 'ramboeck-configurator'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Leads List -->
        <div class="rsc-admin-filters" style="background: #fff; padding: 15px; margin: 20px 0; border: 1px solid #ccd0d4;">
            <form method="get" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <input type="hidden" name="page" value="ramboeck-configurator">

                <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Suchen...', 'ramboeck-configurator'); ?>" class="regular-text">

                <select name="filter_status">
                    <option value=""><?php _e('Alle Status', 'ramboeck-configurator'); ?></option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?php echo esc_attr($status); ?>" <?php selected($filter_status, $status); ?>>
                            <?php echo ucfirst($status); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="filter_industry">
                    <option value=""><?php _e('Alle Branchen', 'ramboeck-configurator'); ?></option>
                    <?php foreach ($industries as $industry): ?>
                        <option value="<?php echo esc_attr($industry); ?>" <?php selected($filter_industry, $industry); ?>>
                            <?php echo esc_html($industry); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="button"><?php _e('Filtern', 'ramboeck-configurator'); ?></button>
                <a href="?page=ramboeck-configurator" class="button"><?php _e('Zurücksetzen', 'ramboeck-configurator'); ?></a>
            </form>
        </div>

        <div class="rsc-admin-card">
            <p><strong><?php printf(__('Anzahl Anfragen: %d', 'ramboeck-configurator'), count($leads)); ?></strong></p>

            <?php if (empty($leads)): ?>
                <p><?php _e('Noch keine Anfragen vorhanden.', 'ramboeck-configurator'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 5%;"><?php _e('ID', 'ramboeck-configurator'); ?></th>
                            <th style="width: 15%;"><?php _e('Datum', 'ramboeck-configurator'); ?></th>
                            <th style="width: 20%;"><?php _e('Kontakt', 'ramboeck-configurator'); ?></th>
                            <th style="width: 15%;"><?php _e('Firma', 'ramboeck-configurator'); ?></th>
                            <th style="width: 10%;"><?php _e('Branche', 'ramboeck-configurator'); ?></th>
                            <th style="width: 10%;"><?php _e('Einrichtung', 'ramboeck-configurator'); ?></th>
                            <th style="width: 10%;"><?php _e('Monatlich', 'ramboeck-configurator'); ?></th>
                            <th style="width: 10%;"><?php _e('Status', 'ramboeck-configurator'); ?></th>
                            <th style="width: 5%;"><?php _e('Aktionen', 'ramboeck-configurator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $lead): ?>
                            <tr>
                                <td><?php echo $lead->id; ?></td>
                                <td><?php echo date_i18n(get_option('date_format'), strtotime($lead->created_at)); ?></td>
                                <td>
                                    <strong><?php echo esc_html($lead->name); ?></strong><br>
                                    <small><?php echo esc_html($lead->email); ?></small>
                                </td>
                                <td><?php echo esc_html($lead->company); ?></td>
                                <td><?php echo esc_html($lead->industry); ?></td>
                                <td><?php echo number_format($lead->total_setup, 2, ',', '.'); ?> EUR</td>
                                <td><?php echo number_format($lead->total_monthly, 2, ',', '.'); ?> EUR</td>
                                <td>
                                    <?php
                                    $status_colors = array(
                                        'new' => '#00a0d2',
                                        'contacted' => '#ffb900',
                                        'quoted' => '#826eb4',
                                        'won' => '#46b450',
                                        'lost' => '#dc3232'
                                    );
                                    $color = $status_colors[$lead->status] ?? '#999';
                                    ?>
                                    <span style="color: <?php echo $color; ?>;">● <?php echo ucfirst($lead->status); ?></span>
                                </td>
                                <td>
                                    <a href="?page=ramboeck-configurator&view=<?php echo $lead->id; ?>" class="button button-small">
                                        <?php _e('Details', 'ramboeck-configurator'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.rsc-admin-card {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
</style>
