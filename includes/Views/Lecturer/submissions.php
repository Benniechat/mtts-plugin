<?php
// submissions.php - For Lecturers
$assignment = \MttsLms\Models\Assignment::find( $_GET['assignment_id'] );
?>
<div class="mtts-dashboard-section">
    <h2>Submissions for: <?php echo esc_html( $assignment->title ); ?></h2>
    <p><a href="?view=assignments">&larr; Back to Assignments</a></p>

    <div class="mtts-card">
        <table class="mtts-table-list">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Submitted At</th>
                    <th>Content Preview</th>
                    <th>Plagiarism Check</th>
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $submissions ) ) : ?>
                    <tr><td colspan="6">No submissions yet.</td></tr>
                <?php else : ?>
                    <?php foreach ( $submissions as $sub ) : 
                         $student_profile = \MttsLms\Models\Student::find( $sub->student_id );
                         $matric = $student_profile ? $student_profile->matric_number : 'Unknown';
                    ?>
                        <tr>
                            <td><?php echo esc_html( $matric ); ?></td>
                            <td><?php echo esc_html( $sub->submitted_at ); ?></td>
                            <td><?php echo esc_html( wp_trim_words( $sub->content, 10 ) ); ?></td>
                            <td>
                                <?php 
                                    $score = floatval( $sub->plagiarism_score );
                                    $badge_class = 'success';
                                    if ( $score > 20 ) $badge_class = 'warning';
                                    if ( $score > 50 ) $badge_class = 'danger';
                                ?>
                                <span class="mtts-badge mtts-badge-<?php echo $badge_class; ?>">
                                    <?php echo number_format( $score, 1 ) . '% Similarity'; ?>
                                </span>
                            </td>
                            <td><?php echo $sub->grade ? $sub->grade : '-'; ?></td>
                            <td>
                                <!-- Simple Grade Form -->
                                <form method="post" action="" style="display: inline-block;">
                                    <input type="hidden" name="mtts_action" value="grade_submission">
                                    <input type="hidden" name="submission_id" value="<?php echo $sub->id; ?>">
                                    <?php wp_nonce_field( 'mtts_grade_submission' ); ?>
                                    <input type="number" name="grade" style="width: 60px;" placeholder="0-100" required>
                                    <button type="submit" class="button button-small">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
