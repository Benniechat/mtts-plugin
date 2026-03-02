<div class="wrap">
    <h1 class="wp-heading-inline">Courses</h1>
    <a href="#" class="page-title-action" onclick="document.getElementById('add-course-form').style.display='block'; return false;">Add New</a>
    <hr class="wp-header-end">

    <div id="mtts-form-container" style="display: none; margin-bottom: 20px; border: 1px solid #ccc; padding: 15px; background: #fff;">
        <h2 id="form-title">Add New Course</h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="course-form">
            <input type="hidden" name="action" value="mtts_save_course">
            <input type="hidden" name="id" id="course-id" value="0">
            <?php wp_nonce_field( 'mtts_save_course' ); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="course_code">Course Code</label></th>
                    <td><input type="text" name="course_code" id="course_code" class="regular-text" required placeholder="e.g., THEO101"></td>
                </tr>
                <tr>
                    <th><label for="course_title">Course Title</label></th>
                    <td><input type="text" name="course_title" id="course_title" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="credit_unit">Credit Unit</label></th>
                    <td><input type="number" name="credit_unit" id="credit_unit" min="1" max="6" value="2" required></td>
                </tr>
                <tr>
                    <th><label for="program_id">Program</label></th>
                    <td>
                        <select name="program_id" id="program_id" required>
                            <option value="">Select Program</option>
                            <?php foreach($programs as $prog): ?>
                                <option value="<?php echo esc_attr($prog->id); ?>"><?php echo esc_html($prog->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="level">Level</label></th>
                    <td>
                        <select name="level" id="level">
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="300">300</option>
                            <option value="400">400</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="semester">Semester</label></th>
                    <td>
                        <select name="semester" id="semester">
                            <option value="1">First Semester</option>
                            <option value="2">Second Semester</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="lecturer_id">Lecturer</label></th>
                    <td>
                        <select name="lecturer_id" id="lecturer_id">
                            <option value="0">Unassigned</option>
                            <?php foreach($lecturers as $lec): ?>
                                <option value="<?php echo esc_attr($lec->ID); ?>"><?php echo esc_html($lec->display_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" id="submit-btn" class="button button-primary" value="Save Course">
                <button type="button" class="button" onclick="resetCourseForm()">Cancel</button>
            </p>
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Code</th>
                <th>Title</th>
                <th>Unit</th>
                <th>Level</th>
                <th>Semester</th>
                <th>Lecturer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $courses ) ) : ?>
                <?php foreach ( $courses as $course ) : ?>
                    <tr>
                        <td><?php echo esc_html( $course->course_code ); ?></td>
                        <td><?php echo esc_html( $course->course_title ); ?></td>
                        <td><?php echo esc_html( $course->credit_unit ); ?></td>
                        <td><?php echo esc_html( $course->level ); ?></td>
                        <td><?php echo esc_html( $course->semester ); ?></td>
                        <td>
                            <?php 
                            if ($course->lecturer_id) {
                                $lec = get_userdata($course->lecturer_id);
                                echo $lec ? esc_html($lec->display_name) : 'Unknown';
                            } else {
                                echo '<span style="color:#94a3b8;">Unassigned</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="#" class="button button-small" onclick="editCourse(<?php echo htmlspecialchars(json_encode($course)); ?>); return false;">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6">No courses found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function editCourse(course) {
    document.getElementById('mtts-form-container').style.display = 'block';
    document.getElementById('form-title').innerText = 'Edit Course: ' + course.course_title;
    document.getElementById('course-id').value = course.id;
    document.getElementById('course_code').value = course.course_code;
    document.getElementById('course_title').value = course.course_title;
    document.getElementById('credit_unit').value = course.credit_unit;
    document.getElementById('program_id').value = course.program_id;
    document.getElementById('level').value = course.level;
    document.getElementById('semester').value = course.semester;
    document.getElementById('lecturer_id').value = course.lecturer_id || 0;
    document.getElementById('submit-btn').value = 'Update Course';
    window.scrollTo(0, 0);
}

function resetCourseForm() {
    document.getElementById('course-form').reset();
    document.getElementById('course-id').value = '0';
    document.getElementById('form-title').innerText = 'Add New Course';
    document.getElementById('submit-btn').value = 'Save Course';
    document.getElementById('mtts-form-container').style.display = 'none';
}
</script>
