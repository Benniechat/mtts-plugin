<?php
// assignments.php - For Students
?>
<h2>My Assignments</h2>

<?php if ( isset( $message ) ) echo $message; ?>

<div class="mtts-card">
    <table class="mtts-table-list">
        <thead>
            <tr>
                <th>Course</th>
                <th>Assignment</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $assignments ) ) : ?>
                <tr><td colspan="5">No pending assignments.</td></tr>
            <?php else : ?>
                <?php foreach ( $assignments as $assignment ) : 
                    $course = \MttsLms\Models\Course::find( $assignment->course_id );
                    $submission = \MttsLms\Models\Submission::get_by_student_assignment( $student->id, $assignment->id ); // Need to implement this method or use generic
                    
                    // Manual fetch for now if method missing
                    global $wpdb;
                    $table = $wpdb->prefix . 'mtts_submissions';
                    $submission = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE student_id = %d AND assignment_id = %d", $student->id, $assignment->id ) );

                    $status = $submission ? 'Submitted' : 'Pending';
                    $grade = ($submission && $submission->grade) ? $submission->grade : '-';
                ?>
                    <tr>
                        <td><?php echo $course ? esc_html( $course->course_code ) : 'N/A'; ?></td>
                        <td><?php echo esc_html( $assignment->title ); ?></td>
                        <td><?php echo esc_html( $assignment->due_date ); ?></td>
                        <td>
                            <span class="mtts-badge mtts-badge-<?php echo $status == 'Submitted' ? 'success' : 'warning'; ?>">
                                <?php echo $status; ?>
                            </span>
                            <?php if ( $grade !== '-' ) echo " (Grade: $grade)"; ?>
                        </td>
                        <td>
                            <?php if ( ! $submission ) : ?>
                                <button class="mtts-btn mtts-btn-sm" onclick="document.getElementById('submit-form-<?php echo $assignment->id; ?>').style.display='block'">Submit</button>
                                
                                <div id="submit-form-<?php echo $assignment->id; ?>" style="display:none; margin-top: 10px; padding: 10px; border: 1px solid #eee;">
                                    <form method="post" action="">
                                        <input type="hidden" name="mtts_action" value="submit_assignment">
                                        <input type="hidden" name="assignment_id" value="<?php echo $assignment->id; ?>">
                                        <?php wp_nonce_field( 'mtts_submit_assignment' ); ?>
                                        
                                        <textarea name="content" class="mtts-form-control" rows="5" placeholder="Type your answer or paste content here..." required></textarea>
                                        <br>
                                        <button type="submit" class="button button-primary">Submit Assignment</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="dashicons dashicons-yes"></span> Done
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
