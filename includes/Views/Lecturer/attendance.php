<h2>Attendance Management</h2>

<div class="mtts-card">
    <form method="get" action="">
        <input type="hidden" name="view" value="attendance">
        <div style="display: flex; gap: 10px; align-items: flex-end;">
            <div class="mtts-form-group" style="margin-bottom: 0;">
                <label for="course_id">Select Course</label>
                <select name="course_id" id="course_id" onchange="this.form.submit()">
                    <option value="">-- Select Course --</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?php echo esc_attr($c->id); ?>" <?php selected( $c->id, $selected_course_id ); ?>><?php echo esc_html($c->course_code . ' - ' . $c->course_title); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mtts-form-group" style="margin-bottom: 0;">
                <label for="date">Class Date</label>
                <input type="date" name="date" id="date" value="<?php echo esc_attr($selected_date); ?>" onchange="this.form.submit()">
            </div>
        </div>
    </form>
</div>

<?php if ( $selected_course_id ): ?>
<div class="mtts-card" style="margin-top: 20px;">
    <?php if ( isset( $_GET['status'] ) && $_GET['status'] == 'saved' ) : ?>
        <div class="mtts-alert mtts-alert-success">Attendance saved successfully!</div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="mtts_action" value="save_attendance">
        <input type="hidden" name="course_id" value="<?php echo esc_attr($selected_course_id); ?>">
        <input type="hidden" name="date" value="<?php echo esc_attr($selected_date); ?>">
        <?php wp_nonce_field( 'mtts_save_attendance' ); ?>
        
        <table class="mtts-table-list">
            <thead>
                <tr>
                    <th>Matric Number</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $students ) ) : ?>
                    <?php foreach ( $students as $student ) : ?>
                        <?php 
                            // Check existing attendance
                            $status = 'present'; // Default
                            foreach($existing_attendance as $att) {
                                if($att->student_id == $student->id) {
                                    $status = $att->status;
                                    break;
                                }
                            }
                            $student_user = get_user_by('id', $student->user_id);
                        ?>
                        <tr>
                            <td><?php echo esc_html( $student->matric_number ); ?></td>
                            <td><?php echo esc_html( $student_user ? $student_user->display_name : 'Unknown' ); ?></td>
                            <td>
                                <label><input type="radio" name="attendance[<?php echo $student->id; ?>]" value="present" <?php checked($status, 'present'); ?>> Present</label> &nbsp;
                                <label><input type="radio" name="attendance[<?php echo $student->id; ?>]" value="absent" <?php checked($status, 'absent'); ?>> Absent</label> &nbsp;
                                <label><input type="radio" name="attendance[<?php echo $student->id; ?>]" value="excused" <?php checked($status, 'excused'); ?>> Excused</label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="3">No students registered for this course yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if ( ! empty( $students ) ) : ?>
        <div style="margin-top: 20px;">
            <button type="submit" name="mtts_save_attendance" class="mtts-btn mtts-btn-primary">Save Attendance</button>
        </div>
        <?php endif; ?>
    </form>
</div>
<?php endif; ?>
