<h2>Course Registration</h2>

<?php if ( isset( $_POST['mtts_register_courses'] ) ): ?>
    <div class="mtts-alert mtts-alert-success">Courses registered successfully!</div>
<?php endif; ?>

<div class="mtts-card">
    <h3>Available Courses</h3>
    <p>Program: <?php echo esc_html( \MttsLms\Models\Program::find($student->program_id)->name ); ?> | Level: <?php echo esc_html( $student->current_level ); ?></p>
    
    <form method="post" action="">
        <input type="hidden" name="mtts_action" value="register_courses">
        <?php wp_nonce_field( 'mtts_register_courses' ); ?>
        
        <table class="mtts-table-list">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Unit</th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $available_courses ) ) : ?>
                    <?php foreach ( $available_courses as $course ) : ?>
                        <?php 
                            $is_registered = false;
                            foreach($registered_courses as $reg) {
                                if($reg->course_id == $course->id) {
                                    $is_registered = true;
                                    break;
                                }
                            }
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="course_ids[]" value="<?php echo esc_attr($course->id); ?>" 
                                    <?php echo $is_registered ? 'checked disabled' : ''; ?>>
                            </td>
                            <td><?php echo esc_html( $course->course_code ); ?></td>
                            <td><?php echo esc_html( $course->course_title ); ?></td>
                            <td><?php echo esc_html( $course->credit_unit ); ?></td>
                            <td><?php echo esc_html( $course->semester ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="5">No courses found for this level.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            <button type="submit" name="mtts_register_courses" class="mtts-btn mtts-btn-primary">Register Selected Courses</button>
        </div>
    </form>
</div>

<div class="mtts-card" style="margin-top: 30px;">
    <h3>Registered Courses (Current Session)</h3>
    <table class="mtts-table-list">
        <thead>
            <tr>
                <th>Code</th>
                <th>Title</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $registered_courses ) ) : ?>
                <?php foreach ( $registered_courses as $reg ) : ?>
                    <?php $course = \MttsLms\Models\Course::find($reg->course_id); ?>
                    <tr>
                        <td><?php echo esc_html( $course->course_code ); ?></td>
                        <td><?php echo esc_html( $course->course_title ); ?></td>
                        <td><?php echo esc_html( $course->credit_unit ); ?></td>
                        <td><?php echo esc_html( ucfirst($reg->status) ); ?></td>
                         <td>
                            <a href="?view=exams&action=take_exam&course_id=<?php echo $reg->course_id; ?>" class="mtts-btn mtts-btn-small">Take Exam</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <!-- Update header colspan -->
            <?php else : ?>
                <tr><td colspan="5">No registered courses yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
