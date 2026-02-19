<div class="mtts-exam-interface">
    <div class="mtts-card">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
            <h3><?php echo esc_html( $course->course_title ); ?> - Exam</h3>
            <div id="mtts-timer" style="font-size: 1.5rem; font-weight: bold; color: var(--mtts-primary);">
                Time Left: <span id="time-display">00:00</span>
            </div>
        </div>
        
        <form method="post" action="" id="mtts-exam-form">
            <input type="hidden" name="course_id" value="<?php echo esc_attr( $course->id ); ?>">
            <input type="hidden" name="mtts_submit_exam" value="1">
            <?php wp_nonce_field( 'mtts_submit_exam' ); ?>

            <?php foreach ( $questions as $index => $q ) : ?>
                <div class="mtts-question-block" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                    <p><strong><?php echo ($index + 1) . '. ' . esc_html( $q->question_text ); ?></strong></p>
                    
                    <div class="mtts-options">
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="radio" name="answers[<?php echo $q->id; ?>]" value="a"> 
                            <?php echo esc_html( $q->option_a ); ?>
                        </label>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="radio" name="answers[<?php echo $q->id; ?>]" value="b"> 
                            <?php echo esc_html( $q->option_b ); ?>
                        </label>
                        <?php if ( ! empty( $q->option_c ) ) : ?>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="radio" name="answers[<?php echo $q->id; ?>]" value="c"> 
                            <?php echo esc_html( $q->option_c ); ?>
                        </label>
                        <?php endif; ?>
                        <?php if ( ! empty( $q->option_d ) ) : ?>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="radio" name="answers[<?php echo $q->id; ?>]" value="d"> 
                            <?php echo esc_html( $q->option_d ); ?>
                        </label>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="mtts-btn mtts-btn-primary" onclick="return confirm('Are you sure you want to submit?');">Submit Exam</button>
        </form>
    </div>
</div>

<script>
    var remainingTime = <?php echo intval( $remaining_time ); ?>;
    
    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        var interval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(interval);
                alert("Time's up! Your exam will be submitted automatically.");
                document.getElementById("mtts-exam-form").submit();
            }
        }, 1000);
    }

    window.onload = function () {
        var display = document.querySelector('#time-display');
        startTimer(remainingTime, display);
    };
</script>
