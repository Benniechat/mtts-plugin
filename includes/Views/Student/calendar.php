<h2>Academic Calendar</h2>

<div class="mtts-card">
    <?php if ( $calendar_url = get_option( 'mtts_academic_calendar_url' ) ) : ?>
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="<?php echo esc_url( $calendar_url ); ?>" target="_blank" class="mtts-btn mtts-btn-primary">Download Official Calendar (PDF)</a>
        </div>
        <hr>
    <?php endif; ?>

    <h3>Important Dates</h3>
    <table class="mtts-table-list">
        <thead>
            <tr>
                <th>Date</th>
                <th>Event</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $events ) ) : ?>
                <tr><td colspan="3">No upcoming events.</td></tr>
            <?php else : ?>
                <?php foreach ( $events as $event ) : ?>
                    <tr>
                        <td>
                            <?php echo date( 'M j, Y', strtotime( $event->start_date ) ); ?>
                            <?php if ( $event->end_date && $event->end_date != $event->start_date ) echo ' - ' . date( 'M j', strtotime( $event->end_date ) ); ?>
                        </td>
                        <td>
                            <strong><?php echo esc_html( $event->title ); ?></strong><br>
                            <?php echo esc_html( $event->description ); ?>
                        </td>
                        <td>
                            <span class="mtts-badge mtts-badge-default"><?php echo ucfirst( $event->type ); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
