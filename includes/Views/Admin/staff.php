<div class="wrap">
    <h1 class="wp-heading-inline">Staff Members</h1>
    <a href="<?php echo admin_url('user-new.php'); ?>" class="page-title-action">Add New</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $staff_members ) ) : ?>
                <?php foreach ( $staff_members as $user ) : ?>
                    <tr>
                        <td><?php echo esc_html( $user->user_login ); ?></td>
                        <td><?php echo esc_html( $user->display_name ); ?></td>
                        <td><?php echo esc_html( $user->user_email ); ?></td>
                        <td><?php echo esc_html( implode( ', ', $user->roles ) ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'user-edit.php?user_id=' . $user->ID ); ?>" class="button button-small">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5">No staff found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
