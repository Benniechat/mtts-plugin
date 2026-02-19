<div class="wrap">
    <h1 class="wp-heading-inline">Students</h1>
    <a href="#" class="page-title-action">Add New (Use Admission)</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Matric No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Level</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $students ) ) : ?>
                <?php foreach ( $students as $student ) : ?>
                    <?php $prog = \MttsLms\Models\Program::find($student->program_id); ?>
                    <tr>
                        <td><?php echo esc_html( $student->matric_number ); ?></td>
                        <td><?php echo esc_html( $student->display_name ); ?></td>
                        <td><?php echo esc_html( $student->user_email ); ?></td>
                        <td><?php echo esc_html( $prog ? $prog->code : 'N/A' ); ?></td>
                        <td><?php echo esc_html( $student->current_level ); ?></td>
                        <td><?php echo esc_html( ucfirst( $student->status ) ); ?></td>
                        <td>
                            <a href="#" class="button button-small">Edit</a>
                            <a href="#" class="button button-small">View Profile</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="7">No students found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
