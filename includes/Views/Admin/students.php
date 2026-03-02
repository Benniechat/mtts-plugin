<div class="wrap">
    <h1 class="wp-heading-inline">Students</h1>
    <a href="#" class="page-title-action">Add New (Use Admission)</a>
    <hr class="wp-header-end">

    <div id="mtts-form-container" style="display: none; margin-bottom: 20px; border: 1px solid #ccc; padding: 15px; background: #fff;">
        <h2 id="form-title">Edit Student Profile</h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="student-form">
            <input type="hidden" name="action" value="mtts_save_student">
            <input type="hidden" name="id" id="student-id" value="0">
            <?php wp_nonce_field( 'mtts_save_student' ); ?>
            
            <table class="form-table">
                <tr>
                    <th><label>Student Name</label></th>
                    <td><input type="text" id="display_name" class="regular-text" readonly></td>
                </tr>
                <tr>
                    <th><label for="current_level">Current Level</label></th>
                    <td>
                        <select name="current_level" id="current_level">
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="300">300</option>
                            <option value="400">400</option>
                            <option value="500">500</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="program_id">Program</label></th>
                    <td>
                        <select name="program_id" id="program_id">
                            <?php foreach($programs as $prog): ?>
                                <option value="<?php echo $prog->id; ?>"><?php echo esc_html($prog->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="campus_center_id">Campus Center</label></th>
                    <td>
                        <select name="campus_center_id" id="campus_center_id">
                            <?php foreach($campuses as $campus): ?>
                                <option value="<?php echo $campus->id; ?>"><?php echo esc_html($campus->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="status">Status</label></th>
                    <td>
                        <select name="status" id="status">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="graduated">Graduated</option>
                            <option value="withdrawn">Withdrawn</option>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="Update Student">
                <button type="button" class="button" onclick="resetStudentForm()">Cancel</button>
            </p>
        </form>
    </div>

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
                    <?php 
                        $prog = \MttsLms\Models\Program::find($student->program_id); 
                    ?>
                    <tr>
                        <td><?php echo esc_html( $student->matric_number ); ?></td>
                        <td><?php echo esc_html( $student->display_name ); ?></td>
                        <td><?php echo esc_html( $student->user_email ); ?></td>
                        <td><?php echo esc_html( $prog ? $prog->code : 'N/A' ); ?></td>
                        <td><?php echo esc_html( $student->current_level ); ?></td>
                        <td><?php echo esc_html( ucfirst( $student->status ) ); ?></td>
                        <td>
                            <a href="#" class="button button-small" onclick="editStudent(<?php echo htmlspecialchars(json_encode($student)); ?>); return false;">Edit</a>
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin-post.php?action=mtts_delete_user&id=' . $student->user_id ), 'mtts_delete_user_' . $student->user_id ); ?>" 
                               class="button button-small" style="color:#ef4444;" 
                               onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
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

<script>
function editStudent(student) {
    document.getElementById('mtts-form-container').style.display = 'block';
    document.getElementById('student-id').value = student.id;
    document.getElementById('display_name').value = student.display_name;
    document.getElementById('current_level').value = student.current_level;
    document.getElementById('program_id').value = student.program_id;
    document.getElementById('campus_center_id').value = student.campus_center_id;
    document.getElementById('status').value = student.status;
    window.scrollTo(0, 0);
}

function resetStudentForm() {
    document.getElementById('student-form').reset();
    document.getElementById('student-id').value = '0';
    document.getElementById('mtts-form-container').style.display = 'none';
}
</script>
