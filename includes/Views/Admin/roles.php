<?php
/**
 * Admin Role & Capability Manager View
 */
?>
<div class="wrap mtts-roles-manager">
    <h1 class="wp-heading-inline">Role & Capability Manager</h1>
    <hr class="wp-header-end">

    <?php if ( isset( $_GET['message'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Changes saved successfully.</p></div>
    <?php endif; ?>

    <div class="mtts-role-grid" style="display: grid; grid-template-columns: 350px 1fr; gap: 30px; margin-top: 20px;">
        
        <!-- Role List & Add New -->
        <div class="mtts-role-sidebar">
            <div class="mtts-card" style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                <h3>Available Roles</h3>
                <ul class="mtts-role-list" style="list-style:none; padding:0;">
                    <?php foreach ( $roles as $role_key => $role_data ) : ?>
                        <li style="margin-bottom:10px; padding:10px; background:#f8fafc; border-radius:6px; display:flex; justify-content:space-between; align-items:center;">
                            <span><?php echo esc_html( $role_data['name'] ); ?> <small>(<?php echo esc_html( $role_key ); ?>)</small></span>
                            <div class="actions">
                                <a href="?page=mtts-roles&edit=<?php echo esc_attr( $role_key ); ?>" class="button button-small">Edit</a>
                                <?php if ( ! in_array( $role_key, array( 'administrator', 'mtts_school_admin', 'mtts_student' ) ) ) : ?>
                                    <a href="<?php echo admin_url( 'admin-post.php?action=mtts_delete_role&role=' . $role_key ); ?>" class="button button-small" style="color:#ef4444;" onclick="return confirm('Delete this role? Users assigned to it may lose access.');">Delete</a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <hr>
                
                <h3>Create New Role</h3>
                <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                    <?php wp_nonce_field( 'mtts_save_role' ); ?>
                    <input type="hidden" name="action" value="mtts_save_role">
                    <input type="hidden" name="role_id" value="">
                    
                    <div class="mtts-form-group">
                        <label>Role ID (Slug)</label>
                        <input type="text" name="role_id" class="mtts-form-control" placeholder="e.g. mtts_accountant" required>
                    </div>
                    <div class="mtts-form-group" style="margin-top:10px;">
                        <label>Display Name</label>
                        <input type="text" name="role_name" class="mtts-form-control" placeholder="e.g. Accountant" required>
                    </div>
                    <button type="submit" class="button button-primary" style="margin-top:15px;">Create Role</button>
                </form>
            </div>
        </div>

        <!-- Capability Editor -->
        <div class="mtts-capability-editor">
            <?php 
            $edit_role_key = isset( $_GET['edit'] ) ? sanitize_key( $_GET['edit'] ) : '';
            if ( $edit_role_key && isset( $roles[ $edit_role_key ] ) ) :
                $edit_role = get_role( $edit_role_key );
            ?>
                <div class="mtts-card" style="background:#fff; padding:30px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                        <?php wp_nonce_field( 'mtts_save_role' ); ?>
                        <input type="hidden" name="action" value="mtts_save_role">
                        <input type="hidden" name="role_id" value="<?php echo esc_attr( $edit_role_key ); ?>">
                        
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; border-bottom:1px solid #eef2f6; padding-bottom:15px;">
                            <h2 style="margin:0;">Editing Privileges: <span style="color:#7c3aed;"><?php echo esc_html( $roles[ $edit_role_key ]['name'] ); ?></span></h2>
                            <button type="submit" class="button button-primary button-large">Save All Privileges</button>
                        </div>

                        <?php foreach ( $capability_groups as $group_name => $caps ) : ?>
                            <div class="mtts-cap-group" style="margin-bottom:30px;">
                                <h3 style="background:#f1f5f9; padding:10px 15px; border-radius:6px; margin-bottom:15px;"><?php echo esc_html( $group_name ); ?></h3>
                                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:15px;">
                                    <?php foreach ( $caps as $cap_key => $cap_label ) : ?>
                                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; padding:12px; border:1px solid #e2e8f0; border-radius:8px; transition:0.2s; background:<?php echo $edit_role->has_cap( $cap_key ) ? '#f5f3ff' : '#fff'; ?>;">
                                            <input type="checkbox" name="caps[]" value="<?php echo esc_attr( $cap_key ); ?>" <?php checked( $edit_role->has_cap( $cap_key ) ); ?>>
                                            <div>
                                                <strong style="display:block;"><?php echo esc_html( $cap_label ); ?></strong>
                                                <small style="color:#64748b;"><?php echo esc_html( $cap_key ); ?></small>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div style="margin-top:30px; text-align:right;">
                            <button type="submit" class="button button-primary button-large">Save All Privileges</button>
                        </div>
                    </form>
                </div>
            <?php else : ?>
                <div class="mtts-card" style="background:#fff; padding:50px; border-radius:8px; text-align:center; color:#94a3b8;">
                    <span class="dashicons dashicons-shield" style="font-size:64px; width:auto; height:auto; margin-bottom:20px;"></span>
                    <h2>Select a role to manage privileges</h2>
                    <p>You can assign capabilities grouped by Ministerial, Financial, and Academic departments.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<style>
.mtts-roles-manager .mtts-form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
}
.mtts-cap-group label:hover {
    border-color: #7c3aed;
    background: #fdfcff;
}
</style>
