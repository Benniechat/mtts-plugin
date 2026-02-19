<h2>Question Bank</h2>

<div class="mtts-card">
    <form method="get" action="">
        <input type="hidden" name="view" value="questions">
        <label for="course_id">Select Course to Manage Questions:</label>
        <select name="course_id" id="course_id" onchange="this.form.submit()">
            <option value="">-- Select Course --</option>
            <?php foreach($courses as $c): ?>
                <option value="<?php echo esc_attr($c->id); ?>" <?php selected( $c->id, $selected_course_id ); ?>>
                    <?php echo esc_html($c->course_code . ' - ' . $c->course_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ( $selected_course_id ): ?>
<div class="mtts-dashboard-grid" style="margin-top: 20px;">
    
    <!-- Add Question Form -->
    <div class="mtts-card">
        <h3>Add New Question</h3>
        <?php if ( isset( $_GET['status'] ) && $_GET['status'] == 'added' ) : ?>
            <div class="mtts-alert mtts-alert-success">Question added successfully!</div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="mtts_action" value="add_question">
            <input type="hidden" name="course_id" value="<?php echo esc_attr($selected_course_id); ?>">
            <?php wp_nonce_field( 'mtts_add_question' ); ?>
            
            <div class="mtts-form-group">
                <label for="question_text">Question Text</label>
                <textarea name="question_text" id="question_text" required class="mtts-form-control" rows="3"></textarea>
            </div>

            <div class="mtts-form-group">
                <label>Options</label>
                <input type="text" name="option_a" placeholder="Option A" required class="mtts-form-control" style="margin-bottom: 5px;">
                <input type="text" name="option_b" placeholder="Option B" required class="mtts-form-control" style="margin-bottom: 5px;">
                <input type="text" name="option_c" placeholder="Option C" class="mtts-form-control" style="margin-bottom: 5px;">
                <input type="text" name="option_d" placeholder="Option D" class="mtts-form-control">
            </div>

            <div class="mtts-form-group">
                <label for="correct_option">Correct Answer</label>
                <select name="correct_option" id="correct_option" class="mtts-form-control" required>
                    <option value="a">Option A</option>
                    <option value="b">Option B</option>
                    <option value="c">Option C</option>
                    <option value="d">Option D</option>
                </select>
            </div>

            <div class="mtts-form-group">
                <label for="points">Points</label>
                <input type="number" name="points" id="points" value="1" min="1" class="mtts-form-control">
            </div>

            <button type="submit" name="mtts_add_question" class="mtts-btn mtts-btn-primary">Add Question</button>
        </form>
    </div>

    <!-- Question List -->
    <div class="mtts-card">
        <h3>Existing Questions (<?php echo count($questions); ?>)</h3>
        <ul class="mtts-list-group">
            <?php if ( ! empty( $questions ) ) : ?>
                <?php foreach ( $questions as $q ) : ?>
                    <li class="mtts-list-item">
                        <p><strong><?php echo esc_html( $q->question_text ); ?></strong> (<?php echo $q->points; ?> pts)</p>
                        <small>Correct: <?php echo strtoupper($q->correct_option); ?></small>
                        <!-- Edit/Delete actions could go here -->
                    </li>
                <?php endforeach; ?>
            <?php else : ?>
                <li>No questions added yet.</li>
            <?php endif; ?>
        </ul>
    </div>

</div>
<?php endif; ?>
