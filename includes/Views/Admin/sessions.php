<div class="wrap">
    <h1 class="wp-heading-inline">Academic Sessions</h1>
    <a href="#" class="page-title-action" onclick="document.getElementById('add-session-form').style.display='block'; return false;">Add New</a>
    <hr class="wp-header-end">

    <div id="mtts-form-container" style="display: none; margin-bottom: 20px; border: 1px solid #ccc; padding: 15px; background: #fff;">
        <h2 id="form-title">Add New Session</h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="session-form">
            <input type="hidden" name="action" value="mtts_save_session">
            <input type="hidden" name="id" id="session-id" value="0">
            <?php wp_nonce_field( 'mtts_save_session' ); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="name">Session Name</label></th>
                    <td><input type="text" name="name" id="name" class="regular-text" required placeholder="e.g., 2024/2025"></td>
                </tr>
                <tr>
                    <th><label for="start_date">Start Date</label></th>
                    <td><input type="date" name="start_date" id="start_date" required></td>
                </tr>
                <tr>
                    <th><label for="end_date">End Date</label></th>
                    <td><input type="date" name="end_date" id="end_date" required></td>
                </tr>
                <tr>
                    <th><label for="status">Status</label></th>
                    <td>
                        <select name="status" id="status">
                            <option value="inactive">Inactive</option>
                            <option value="active">Active</option>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" id="submit-btn" class="button button-primary" value="Save Session">
                <button type="button" class="button" onclick="resetSessionForm()">Cancel</button>
            </p>
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $sessions ) ) : ?>
                <?php foreach ( $sessions as $session ) : ?>
                    <tr>
                        <td><?php echo esc_html( $session->name ); ?></td>
                        <td><?php echo esc_html( $session->start_date ); ?></td>
                        <td><?php echo esc_html( $session->end_date ); ?></td>
                        <td><?php echo esc_html( $session->status ); ?></td>
                        <td>
                            <a href="#" class="button button-small" onclick="editSession(<?php echo htmlspecialchars(json_encode($session)); ?>); return false;">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5">No sessions found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function editSession(session) {
    document.getElementById('mtts-form-container').style.display = 'block';
    document.getElementById('form-title').innerText = 'Edit Session: ' + session.name;
    document.getElementById('session-id').value = session.id;
    document.getElementById('name').value = session.name;
    document.getElementById('start_date').value = session.start_date;
    document.getElementById('end_date').value = session.end_date;
    document.getElementById('status').value = session.status;
    document.getElementById('submit-btn').value = 'Update Session';
    window.scrollTo(0, 0);
}

function resetSessionForm() {
    document.getElementById('session-form').reset();
    document.getElementById('session-id').value = '0';
    document.getElementById('form-title').innerText = 'Add New Session';
    document.getElementById('submit-btn').value = 'Save Session';
    document.getElementById('mtts-form-container').style.display = 'none';
}
</script>
