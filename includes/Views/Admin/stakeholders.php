<div class="wrap">
    <h1>Stakeholder Registration & Management</h1>
    <p>Register and manage non-student stakeholders such as Alumni and Guests.</p>

    <div class="mtts-admin-card" style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); margin-bottom:30px;">
        <h2 id="form-title">Register New Stakeholder</h2>
        <form method="post" action="" id="stakeholder-form">
            <?php wp_nonce_field( 'mtts_register_stakeholder' ); ?>
            <input type="hidden" name="mtts_register_stakeholder" value="1">
            <input type="hidden" name="user_id" id="user-id" value="0">
            
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
                <button type="button" class="button" id="cancel-edit" style="display:none;" onclick="resetStakeholderForm()">Cancel Edit</button>
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $stakeholders ) : ?>
                <?php foreach ( $stakeholders as $user ) : ?>
                    <?php 
                        $data = array(
                            'id'         => $user->ID,
                            'first_name' => $user->first_name,
                            'last_name'  => $user->last_name,
                            'email'      => $user->user_email,
                            'role'       => !empty($user->roles) ? $user->roles[0] : 'mtts_guest'
                        );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $user->display_name ); ?></strong></td>
                        <td><?php echo esc_html( $user->user_email ); ?></td>
                        <td><?php echo esc_html( implode( ', ', $user->roles ) ); ?></td>
                        <td><?php echo esc_html( $user->user_registered ); ?></td>
                        <td>
                            <a href="#" class="button button-small" onclick="editStakeholder(<?php echo htmlspecialchars(json_encode($data)); ?>); return false;">Edit</a>
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=mtts-stakeholders&action=delete&id=' . $user->ID ), 'mtts_delete_stakeholder_' . $user->ID ); ?>" class="button button-small" onclick="return confirm('Are you sure you want to delete this stakeholder?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5">No stakeholders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function editStakeholder(data) {
    document.getElementById('form-title').innerText = 'Edit Stakeholder';
    document.getElementById('user-id').value = data.id;
    document.getElementById('first_name').value = data.first_name;
    document.getElementById('last_name').value = data.last_name;
    document.getElementById('email').value = data.email;
    document.getElementById('role').value = data.role;
    document.getElementById('submit').value = 'Update Stakeholder';
    document.getElementById('cancel-edit').style.display = 'inline-block';
    window.scrollTo(0, 0);
}

function resetStakeholderForm() {
    document.getElementById('stakeholder-form').reset();
    document.getElementById('form-title').innerText = 'Register New Stakeholder';
    document.getElementById('user-id').value = '0';
    document.getElementById('submit').value = 'Register Stakeholder';
    document.getElementById('cancel-edit').style.display = 'none';
}
</script>
