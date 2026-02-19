<?php
/**
 * Admin View: Campus Centers Management
 * Allows admin to manage campus centers used in matric number generation.
 * Matric Format: MTTS/{YEAR}/{CAMPUS_CODE}/{SERIAL}
 */
?>
<div class="wrap">
    <h1>Campus Centers</h1>
    <p>Campus center codes are used to generate student matric numbers in the format: <strong>MTTS/YEAR/CODE/SERIAL</strong> (e.g. <code>MTTS/2026/LAG/001</code>)</p>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Campus center saved successfully.</p></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">

        <!-- Add New Campus Center -->
        <div>
            <h2>Add New Campus Center</h2>
            <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                <input type="hidden" name="action" value="mtts_save_campus_center">
                <?php wp_nonce_field( 'mtts_save_campus_center' ); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="name">Campus Name</label></th>
                        <td><input type="text" name="name" id="name" class="regular-text" placeholder="e.g. Lagos" required></td>
                    </tr>
                    <tr>
                        <th><label for="code">Campus Code</label></th>
                        <td>
                            <input type="text" name="code" id="code" class="small-text" maxlength="10" placeholder="e.g. LAG" required style="text-transform:uppercase;">
                            <p class="description">2–5 uppercase letters. This appears in the matric number.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="city">City</label></th>
                        <td><input type="text" name="city" id="city" class="regular-text" placeholder="e.g. Lagos"></td>
                    </tr>
                    <tr>
                        <th><label for="state">State</label></th>
                        <td><input type="text" name="state" id="state" class="regular-text" placeholder="e.g. Lagos State"></td>
                    </tr>
                </table>
                <?php submit_button( 'Add Campus Center' ); ?>
            </form>
        </div>

        <!-- Existing Campus Centers -->
        <div>
            <h2>Existing Campus Centers</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $campus_centers ) ) : ?>
                        <?php foreach ( $campus_centers as $center ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $center->name ); ?></strong></td>
                                <td><code><?php echo esc_html( $center->code ); ?></code></td>
                                <td><?php echo esc_html( $center->city ); ?></td>
                                <td><?php echo esc_html( $center->state ); ?></td>
                                <td>
                                    <?php if ( $center->is_active ) : ?>
                                        <span style="color: green;">&#10003; Active</span>
                                    <?php else : ?>
                                        <span style="color: red;">&#10007; Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5">No campus centers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px; padding: 15px; background: #f0f0f1; border-left: 4px solid #7c3aed;">
                <strong>Matric Number Preview:</strong><br>
                <code>MTTS / <?php echo date('Y'); ?> / [CODE] / 001</code><br>
                <small>Example: <strong>MTTS/<?php echo date('Y'); ?>/LAG/001</strong></small>
            </div>
        </div>

    </div>
</div>
