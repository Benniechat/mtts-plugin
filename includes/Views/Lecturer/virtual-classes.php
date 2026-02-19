<h2 style="margin-bottom: 20px;">Virtual Classroom</h2>

<div class="mtts-card">
    <h3>Schedule New Class</h3>
    <form method="post" action="">
        <input type="hidden" name="mtts_action" value="create_meeting">
        <?php wp_nonce_field( 'mtts_create_meeting' ); ?>

        <div class="mtts-form-group">
            <label>Topic</label>
            <input type="text" name="topic" class="mtts-form-control" required placeholder="e.g. THL 101 - Introduction to Theology">
        </div>

        <div class="mtts-form-group">
            <label>Start Time</label>
            <input type="datetime-local" name="start_time" class="mtts-form-control" required>
        </div>

        <div class="mtts-form-group">
            <label>Duration (minutes)</label>
            <input type="number" name="duration" class="mtts-form-control" value="40" required>
        </div>

        <button type="submit" class="mtts-btn mtts-btn-primary">Schedule Meeting</button>
    </form>
</div>

<div class="mtts-card" style="margin-top: 20px;">
    <h3>Upcoming Classes</h3>
    <p><em>No classes scheduled yet.</em></p>
</div>
