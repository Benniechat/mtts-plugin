<style>
    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }
    .event-card {
        padding: 0;
        overflow: hidden;
    }
    .event-header {
        background: linear-gradient(135deg, var(--mtts-purple), #6a0dad);
        color: #fff;
        padding: 20px;
        text-align: center;
    }
    .event-body {
        padding: 20px;
    }
    .event-date {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 5px;
    }
    .event-month {
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 2px;
        opacity: 0.9;
    }
</style>

<div class="mtts-dashboard-section" style="max-width:1000px; margin:0 auto;">
    <h2 style="color:var(--mtts-purple); margin-bottom:30px;">Ministry Events & Homecomings</h2>

    <div class="events-grid">
        <?php foreach ( $events as $event ) : ?>
            <div class="glass-card event-card">
                <div class="event-header">
                    <div class="event-month"><?php echo date( 'F', strtotime( $event->date ) ); ?></div>
                    <div class="event-date"><?php echo date( 'd', strtotime( $event->date ) ); ?></div>
                    <div style="font-size:0.8rem; opacity:0.8;"><?php echo date( 'Y', strtotime( $event->date ) ); ?></div>
                </div>
                <div class="event-body">
                    <h3 style="margin:0 0 10px; color:var(--mtts-purple); font-size:1.1rem;"><?php echo esc_html( $event->title ); ?></h3>
                    <p style="color:#666; font-size:0.85rem; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                        <span class="dashicons dashicons-location" style="font-size:1rem; color:var(--mtts-gold);"></span>
                        <?php echo esc_html( $event->location ); ?>
                    </p>
                    <p style="font-size:0.9rem; line-height:1.5; color:#444; margin-bottom:20px;"><?php echo esc_html( $event->description ); ?></p>
                    <button class="mtts-btn w-100" style="background:var(--mtts-purple); color:#fff; border-radius:12px; font-weight:600; width:100%;">RSVP & Discern Details</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
