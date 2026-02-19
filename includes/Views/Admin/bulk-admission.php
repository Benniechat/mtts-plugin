<div class="wrap">
    <h1>Bulk Student Admission</h1>

    <?php if ( isset( $_GET['imported'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo intval( $_GET['imported'] ); ?> students imported successfully!</p>
        </div>
    <?php endif; ?>

    <?php 
    $errors = get_transient( 'mtts_bulk_errors' );
    if ( $errors ) : 
        delete_transient( 'mtts_bulk_errors' );
    ?>
        <div class="notice notice-error is-dismissible">
            <p><strong>Errors occurred during import:</strong></p>
            <ul>
                <?php foreach ( $errors as $error ) : ?>
                    <li><?php echo esc_html( $error ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card" style="max-width: 600px; padding: 20px; margin-top: 20px;">
        <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="mtts_bulk_admission">
            <?php wp_nonce_field( 'mtts_bulk_admission' ); ?>

            <table class="form-table">
                <tr>
                    <th><label>Academic Session</label></th>
                    <td>
                        <select name="session_id" required>
                            <?php foreach ( $sessions as $session ) : ?>
                                <option value="<?php echo $session->id; ?>"><?php echo esc_html( $session->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Program</label></th>
                    <td>
                        <select name="program_id" required>
                            <?php foreach ( $programs as $program ) : ?>
                                <option value="<?php echo $program->id; ?>"><?php echo esc_html( $program->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Campus Center</label></th>
                    <td>
                        <select name="campus_id" required>
                            <?php foreach ( $campus_centers as $campus ) : ?>
                                <option value="<?php echo $campus->id; ?>"><?php echo esc_html( $campus->name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Initial Level</label></th>
                    <td><input type="text" name="level" value="100" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label>CSV File</label></th>
                    <td>
                        <input type="file" name="csv_file" accept=".csv" required>
                        <p class="description">Format: Name, Email, Phone</p>
                    </td>
                </tr>
            </table>

            <?php submit_button( 'Import Students' ); ?>
        </form>
    </div>
</div>
