<div class="wrap">
    <h1>Stakeholder Registration & Management</h1>
    <p>Register and manage non-student stakeholders such as Alumni and Guests.</p>

    <div class="mtts-admin-card" style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); margin-bottom:30px;">
        <h2>Register New Stakeholder</h2>
        <form method="post" action="">
            <?php wp_nonce_field( 'mtts_register_stakeholder' ); ?>
            <input type="hidden" name="mtts_register_stakeholder" value="1">
            
            <table class="form-table">
                <tr>
                    <th><label for="first_name">First Name</label></th>
                    <td><input name="first_name" type="text" id="first_name" value="" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="last_name">Last Name</label></th>
                    <td><input name="last_name" type="text" id="last_name" value="" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="email">Email Address</label></th>
                    <td><input name="email" type="email" id="email" value="" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="role">Role</label></th>
                    <td>
                        <select name="role" id="role">
                            <option value="mtts_alumni">Alumni</option>
                            <option value="mtts_guest">Guest Stakeholder</option>
                        </select>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Register Stakeholder">
            </p>
        </form>
    </div>

    <h2>Existing Stakeholders</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $stakeholders ) : ?>
                <?php foreach ( $stakeholders as $user ) : ?>
                    <tr>
                        <td><strong><?php echo esc_html( $user->display_name ); ?></strong></td>
                        <td><?php echo esc_html( $user->user_email ); ?></td>
                        <td><?php echo esc_html( implode( ', ', $user->roles ) ); ?></td>
                        <td><?php echo esc_html( $user->user_registered ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="4">No stakeholders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
