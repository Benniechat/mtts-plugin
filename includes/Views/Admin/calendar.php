<div class="wrap">
    <h1>Academic Calendar & Events</h1>
    
    <div class="card" style="max_width: 100%; padding: 20px; margin-top: 20px;">
        <h2>Upload Academic Calendar (PDF)</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="mtts_action" value="upload_calendar">
            <?php wp_nonce_field( 'mtts_upload_calendar' ); ?>
            <input type="file" name="calendar_pdf" accept=".pdf" required>
            <submit class="button button-primary">Upload PDF</submit>
        </form>
        <?php if ( $calendar_url = get_option( 'mtts_academic_calendar_url' ) ) : ?>
            <p>Current Calendar: <a href="<?php echo esc_url( $calendar_url ); ?>" target="_blank">Download PDF</a></p>
        <?php endif; ?>
    </div>

    <hr>

    <h2>Important Dates & Events</h2>
    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;" id="mtts-form-container">
            <h3 id="form-title">Add New Event</h3>
            <form method="post" action="" id="event-form">
                <input type="hidden" name="mtts_action" value="save_event">
                <input type="hidden" name="event_id" id="event-id" value="0">
                <?php wp_nonce_field( 'mtts_save_event' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th>Title</th>
                        <td><input type="text" name="title" id="title" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th>Start Date</th>
                        <td><input type="date" name="start_date" id="start_date" required></td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td><input type="date" name="end_date" id="end_date"></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>
                            <select name="type" id="type">
                                <option value="general">General</option>
                                <option value="holiday">Holiday</option>
                                <option value="exam">Exam</option>
                                <option value="resumption">Resumption</option>
                                <option value="deadline">Deadline</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td><textarea name="description" id="description" rows="3" class="large-text"></textarea></td>
                    </tr>
                </table>
                <p>
                    <button type="submit" id="submit-btn" class="button button-primary">Add Event</button>
                    <button type="button" class="button" onclick="resetEventForm()">Cancel</button>
                </p>
            </form>
        </div>

        <div style="flex: 1;">
            <h3>Upcoming Events</h3>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $events ) ) : ?>
                        <tr><td colspan="4">No events found for this session.</td></tr>
                    <?php else : ?>
                        <?php foreach ( $events as $event ) : ?>
                            <tr>
                                <td>
                                    <?php echo date( 'M j, Y', strtotime( $event->start_date ) ); ?>
                                    <?php if ( $event->end_date && $event->end_date != $event->start_date ) echo ' - ' . date( 'M j', strtotime( $event->end_date ) ); ?>
                                </td>
                                <td>
                                    <strong><?php echo esc_html( $event->title ); ?></strong><br>
                                    <small><?php echo esc_html( $event->description ); ?></small>
                                </td>
                                <td><?php echo ucfirst( $event->type ); ?></td>
                                <td>
                                    <a href="#" class="button button-small" onclick="editEvent(<?php echo htmlspecialchars(json_encode($event)); ?>); return false;">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function editEvent(event) {
    document.getElementById('form-title').innerText = 'Edit Event: ' + event.title;
    document.getElementById('event-id').value = event.id;
    document.getElementById('title').value = event.title;
    document.getElementById('start_date').value = event.start_date;
    document.getElementById('end_date').value = event.end_date;
    document.getElementById('type').value = event.type;
    document.getElementById('description').value = event.description;
    document.getElementById('submit-btn').innerText = 'Update Event';
    window.scrollTo(0, 0);
}

function resetEventForm() {
    document.getElementById('event-form').reset();
    document.getElementById('event-id').value = '0';
    document.getElementById('form-title').innerText = 'Add New Event';
    document.getElementById('submit-btn').innerText = 'Add Event';
}
</script>
