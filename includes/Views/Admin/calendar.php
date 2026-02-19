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
        <div style="flex: 1;">
            <h3>Add New Event</h3>
            <form method="post" action="">
                <input type="hidden" name="mtts_action" value="add_event">
                <?php wp_nonce_field( 'mtts_add_event' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th>Title</th>
                        <td><input type="text" name="title" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th>Start Date</th>
                        <td><input type="date" name="start_date" required></td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td><input type="date" name="end_date"></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>
                            <select name="type">
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
                        <td><textarea name="description" rows="3" class="large-text"></textarea></td>
                    </tr>
                </table>
                <p><button type="submit" class="button button-primary">Add Event</button></p>
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
                                    <a href="#" style="color: red;">Delete</a> <!-- Placeholder for delete -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
