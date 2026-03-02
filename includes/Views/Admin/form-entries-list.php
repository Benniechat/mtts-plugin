<?php
/**
 * Form Entries List View
 */
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Form Entries</h1>
    
    <div class="tablenav top">
        <div class="alignleft actions">
            <select id="mtts-form-filter">
                <option value="">All Forms</option>
                <?php foreach ( $forms as $f ) : ?>
                    <option value="<?php echo $f->id; ?>" <?php selected( $form_id, $f->id ); ?>><?php echo esc_html( $f->title ); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="button" onclick="window.location.href='admin.php?page=mtts-form-entries&form_id=' + document.getElementById('mtts-form-filter').value">Filter</button>
            
            <?php if ( $form_id ) : ?>
                <a href="<?php echo admin_url( 'admin-post.php?action=mtts_export_entries_csv&form_id=' . $form_id ); ?>" class="button button-secondary">Export to CSV</a>
            <?php endif; ?>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Form</th>
                <th>User</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $entries ) ) : ?>
                <tr><td colspan="5">No entries found.</td></tr>
            <?php else : ?>
                <?php foreach ( $entries as $entry ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $entry->form_title ); ?></strong></td>
                        <td>
                            <?php 
                            if ( $entry->user_id ) {
                                $userdata = get_userdata( $entry->user_id );
                                echo esc_html( $userdata->display_name );
                            } else {
                                echo '<em>Guest</em>';
                            }
                            ?>
                        </td>
                        <td>
                            <span class="mtts-status mtts-status-<?php echo esc_attr( $entry->status ); ?>">
                                <?php echo ucfirst( $entry->status ); ?>
                            </span>
                        </td>
                        <td><?php echo date( 'M j, Y H:i', strtotime( $entry->created_at ) ); ?></td>
                        <td>
                            <a href="admin.php?page=mtts-form-entries&action=view&entry_id=<?php echo $entry->id; ?>" class="button button-small">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.mtts-status { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
.mtts-status-pending { background: #fef9c3; color: #854d0e; }
.mtts-status-reviewed { background: #eff6ff; color: #1d4ed8; }
.mtts-status-approved { background: #dcfce7; color: #166534; }
.mtts-status-rejected { background: #fee2e2; color: #991b1b; }
</style>
