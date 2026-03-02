<?php
/**
 * Form Entry Details View
 */
$data = json_decode( $entry->entry_data, true );
?>
<div class="wrap">
    <h1>Entry Details: <?php echo esc_html( $entry->form_title ); ?></h1>
    <a href="admin.php?page=mtts-form-entries&form_id=<?php echo $entry->form_id; ?>" class="button">&larr; Back to Entries</a>
    
    <div style="display:grid; grid-template-columns: 1fr 300px; gap:30px; margin-top:20px;">
        
        <div class="mtts-card" style="background:#fff; padding:30px; border-radius:12px; border:1px solid #ddd;">
            <table class="form-table">
                <?php foreach ( $data as $label => $value ) : ?>
                    <tr valign="top">
                        <th scope="row" style="width:250px;"><strong><?php echo esc_html( $label ); ?></strong></th>
                        <td>
                            <?php 
                            if ( is_array( $value ) ) {
                                // Handle Repeater Data
                                echo '<table class="widefat striped" style="margin-top:0;">';
                                foreach ( $value as $row_idx => $row ) {
                                    echo '<tr><td><strong>#' . ($row_idx + 1) . '</strong></td><td>';
                                    foreach ( $row as $k => $v ) {
                                        echo '<strong>' . esc_html(ucwords(str_replace('_', ' ', $k))) . ':</strong> ' . esc_html($v) . '<br>';
                                    }
                                    echo '</td></tr>';
                                }
                                echo '</table>';
                            } elseif ( filter_var($value, FILTER_VALIDATE_URL) && preg_match('/\.(jpg|jpeg|png|gif|pdf)$/i', $value) ) {
                                // Handle Files
                                if ( preg_match('/\.(pdf)$/i', $value) ) {
                                    echo '<a href="' . esc_url( $value ) . '" target="_blank" class="button button-secondary">View PDF</a>';
                                } else {
                                    echo '<img src="' . esc_url( $value ) . '" style="max-width:300px; border-radius:8px; border:1px solid #ddd;"><br>';
                                    echo '<a href="' . esc_url( $value ) . '" target="_blank">View Full Image</a>';
                                }
                            } elseif ( strpos($value, 'data:image/png;base64') === 0 ) {
                                // Handle Signature
                                echo '<img src="' . esc_url( $value ) . '" style="border:1px solid #ddd; background:#f9f9f9; padding:5px; border-radius:5px; max-width:100%;">';
                            } else {
                                echo nl2br( esc_html( $value ) );
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div>
            <div class="mtts-card" style="background:#fff; padding:20px; border-radius:12px; border:1px solid #ddd; border-top: 4px solid #7c3aed;">
                <h3>Entry Meta</h3>
                <aside class="mtts-details-sidebar">
                    <div class="mtts-card status-manager">
                        <h4>Status Management</h4>
                        <div class="mtts-status-badge <?php echo esc_attr($entry->status); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $entry->status)); ?>
                        </div>
                        
                        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-top:20px;">
                            <?php wp_nonce_field('mtts_update_entry_status'); ?>
                            <input type="hidden" name="action" value="mtts_update_entry_status">
                            <input type="hidden" name="entry_id" value="<?php echo $entry->id; ?>">
                            
                            <div class="mtts-form-group">
                                <label>Change Status</label>
                                <select name="status" class="widefat">
                                    <option value="pending" <?php selected($entry->status, 'pending'); ?>>Pending Review</option>
                                    <option value="awaiting_docs" <?php selected($entry->status, 'awaiting_docs'); ?>>Awaiting Documents</option>
                                    <option value="approved" <?php selected($entry->status, 'approved'); ?>>Approve (Activate)</option>
                                    <option value="rejected" <?php selected($entry->status, 'rejected'); ?>>Reject</option>
                                </select>
                            </div>

                            <div class="mtts-form-group">
                                <label>Remarks / Rejection Reason</label>
                                <textarea name="remarks" class="widefat" rows="4" placeholder="Enter notes for the applicant or internal audit..."></textarea>
                            </div>

                            <button type="submit" class="mtts-button mtts-button-primary" style="width:100%;">Update Application</button>
                        </form>
                    </div>

                    <div class="mtts-card audit-trail" style="margin-top:20px;">
                        <h4>Action Audit Log</h4>
                        <div class="mtts-audit-list">
                            <?php 
                            $logs = get_option('mtts_audit_entry_' . $entry->id, array());
                            if ( empty($logs) ) :
                                echo '<p>No actions recorded yet.</p>';
                            else :
                                foreach ( array_reverse($logs) as $log ) : ?>
                                    <div class="mtts-audit-item" style="border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:10px; font-size:12px;">
                                        <strong><?php echo esc_html($log['actor_name']); ?></strong> changed status to 
                                        <span class="status-text-<?php echo esc_attr($log['action']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $log['action'])); ?>
                                        </span>
                                        <br>
                                        <small class="text-muted"><?php echo $log['action_time']; ?></small>
                                        <?php if ( ! empty($log['remarks']) ) : ?>
                                            <p style="margin:5px 0 0; font-style:italic;">"<?php echo esc_html($log['remarks']); ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach;
                            endif; ?>
                        </div>
                    </div>

                    <div class="mtts-card actions" style="margin-top:20px;">
                        <h4>Actions</h4>
                        <button onclick="window.print()" class="mtts-button mtts-button-secondary" style="width:100%; margin-bottom:10px;">
                            <span class="dashicons dashicons-printer"></span> Print Application
                        </button>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

<style>
.mtts-status { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
.mtts-status-pending { background: #fef9c3; color: #854d0e; }
.mtts-status-reviewed { background: #eff6ff; color: #1d4ed8; }
.mtts-status-approved { background: #dcfce7; color: #166534; }
.mtts-status-rejected { background: #fee2e2; color: #991b1b; }
@media print {
    .wp-admin #adminmenuback, .wp-admin #adminmenuwrap, .wp-admin #wpfooter, .button, .tablenav, h3, form { display: none !important; }
    .wrap { margin: 0; padding: 0; }
    .mtts-card { border: none !important; box-shadow: none !important; }
}
</style>
