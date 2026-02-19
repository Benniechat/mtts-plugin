<div class="wrap">
    <h1 class="wp-heading-inline">Student Applications</h1>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Applicant Name</th>
                <th>Program</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $applications ) ) : ?>
                <?php foreach ( $applications as $app ) : ?>
                    <?php 
                        $prog = \MttsLms\Models\Program::find($app->program_id);
                        $prog_name = $prog ? $prog->code : 'Unknown';
                    ?>
                    <tr>
                        <td><?php echo esc_html( $app->applicant_name ); ?></td>
                        <td><?php echo esc_html( $prog_name ); ?></td>
                        <td><?php echo esc_html( $app->submitted_at ); ?></td>
                        <td>
                            <span class="mtts-status mtts-status-<?php echo esc_attr( $app->status ); ?>">
                                <?php echo esc_html( ucfirst( $app->status ) ); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=mtts-applications&view=details&id=' . $app->id ); ?>" class="button button-small">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5">No applications found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
