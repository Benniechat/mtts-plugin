<h2>My Classes</h2>

<div class="mtts-card">
    <div class="mtts-table-responsive">
        <table class="mtts-table-list">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Enrolled Students</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $courses ) ) : ?>
                    <?php foreach ( $courses as $course ) : ?>
                        <tr>
                            <td><?php echo esc_html( $course->course_code ); ?></td>
                            <td><?php echo esc_html( $course->course_title ); ?></td>
                            <td>0</td> <!-- Placeholder for count -->
                            <td>
                                <a href="#" class="mtts-btn mtts-btn-small">View Students</a>
                                <a href="#" class="mtts-btn mtts-btn-small">Schedule Class</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="4">No courses assigned.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
