<?php
// assignments.php - For Lecturers
?>
<div class="mtts-dashboard-section">
    <h2>My Assignments</h2>

    <div class="mtts-card">
        <h3>Create New Assignment</h3>
        <form method="post" action="">
            <input type="hidden" name="mtts_action" value="create_assignment">
            <?php wp_nonce_field( 'mtts_create_assignment' ); ?>
            
            <div class="mtts-form-group">
                <label>Course</label>
                <select name="course_id" class="mtts-form-control" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach( $courses as $course ): ?>
                        <option value="<?php echo $course->id; ?>"><?php echo esc_html( $course->course_title ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mtts-form-group">
                <label>Assignment Title</label>
                <input type="text" name="title" class="mtts-form-control" required>
            </div>

            <div class="mtts-form-group">
                <label>Description (Instructions)</label>
                <textarea name="description" class="mtts-form-control" rows="4"></textarea>
            </div>

            <div class="mtts-form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" class="mtts-form-control" required>
            </div>

            <div class="mtts-form-group">
                <label>Total Points</label>
                <input type="number" name="total_points" class="mtts-form-control" value="100">
            </div>

            <button type="submit" class="mtts-btn mtts-btn-primary">Create Assignment</button>
        </form>
    </div>

    <div class="mtts-card" style="margin-top: 30px;">
        <h3>Existing Assignments</h3>
        <table class="mtts-table-list">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Title</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $assignments ) ) : ?>
                    <tr><td colspan="4">No assignments created yet.</td></tr>
                <?php else : ?>
                    <?php foreach ( $assignments as $assignment ) : 
                        $course = \MttsLms\Models\Course::find( $assignment->course_id );
                    ?>
                        <tr>
                            <td><?php echo $course ? esc_html( $course->course_code ) : 'N/A'; ?></td>
                            <td><?php echo esc_html( $assignment->title ); ?></td>
                            <td><?php echo esc_html( $assignment->due_date ); ?></td>
                            <td>
                                <a href="?view=submissions&assignment_id=<?php echo $assignment->id; ?>" class="mtts-btn mtts-btn-sm">View Submissions</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
