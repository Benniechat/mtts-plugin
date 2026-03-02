<?php
/**
 * Seminary Events – Stitch UI Design
 */
if (!defined('ABSPATH')) exit;

// Pull real events from DB if Event model exists, else use placeholders
try {
    $db_events = \MttsLms\Models\Event::get_upcoming(10);
} catch(\Throwable $e) {
    $db_events = [];
}
// Merge with the static $events array from controller
$all_events = !empty($db_events) ? $db_events : $events;

// My schedule (static placeholder for now)
$my_schedule = [
    ['title' => 'Morning Prayer', 'when' => 'Today, 08:30 AM'],
    ['title' => 'Guest Lecture: Rev. Smith', 'when' => 'Friday, 02:00 PM'],
];
?>

<style>
.st-events-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 24px;
    max-width: 1100px;
    margin: 0 auto;
}
.st-event-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(13,39,165,.08);
    overflow: hidden;
    display: flex;
    transition: box-shadow 0.2s, transform 0.2s;
}
.st-event-card:hover {
    box-shadow: 0 6px 24px rgba(13,39,165,.12);
    transform: translateY(-2px);
}
.st-event-date-badge {
    min-width: 80px;
    text-align: center;
    padding: 20px 10px;
    background: linear-gradient(180deg, #6b21a8, #7c3aed);
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.st-event-date-badge .day { font-size: 32px; font-weight: 800; line-height: 1; }
.st-event-date-badge .month { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; opacity: .9; }
.st-event-date-badge .year { font-size: 11px; opacity: .7; margin-top: 2px; }
.st-event-body { padding: 20px 24px; flex: 1; }
.st-event-types-nav {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.st-events-type-btn {
    padding: 7px 18px;
    border-radius: 100px;
    border: 1.5px solid #e5e7eb;
    background: #fff;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.15s;
}
.st-events-type-btn:hover,
.st-events-type-btn.active {
    background: #6b21a8;
    color: #fff;
    border-color: #6b21a8;
}
@media(max-width:768px) { .st-events-layout { grid-template-columns: 1fr; } }
</style>

<div class="st-events-layout">
    <!-- Main Events Column -->
    <div>
        <div style="margin-bottom:28px;">
            <h2 style="margin:0;font-size:26px;font-weight:800;color:#1a1a2e;">Seminary Events</h2>
            <p style="color:#6b7280;margin:5px 0 0;font-size:14px;">Stay connected with our theological community through upcoming gatherings.</p>
        </div>

        <!-- Filter Tabs -->
        <div class="st-event-types-nav">
            <button class="st-events-type-btn active" onclick="stEventFilter('all',this)">All Events</button>
            <button class="st-events-type-btn" onclick="stEventFilter('campus',this)">🏛 On Campus</button>
            <button class="st-events-type-btn" onclick="stEventFilter('virtual',this)">📹 Virtual</button>
            <button class="st-events-type-btn" onclick="stEventFilter('alumni',this)">🎓 Alumni Only</button>
        </div>

        <!-- Events List -->
        <div id="st-events-container" style="display:flex;flex-direction:column;gap:16px;">
            <?php foreach ($all_events as $event):
                $ev_date = isset($event->date) ? $event->date : (isset($event->event_date) ? $event->event_date : date('Y-m-d'));
                $ev_title = isset($event->title) ? $event->title : (isset($event->event_title) ? $event->event_title : 'Upcoming Event');
                $ev_loc = isset($event->location) ? $event->location : (isset($event->venue) ? $event->venue : 'Location TBD');
                $ev_desc = isset($event->description) ? $event->description : '';
                $is_virtual = stripos($ev_loc, 'virtual') !== false || stripos($ev_loc, 'zoom') !== false || stripos($ev_loc, 'online') !== false;
            ?>
            <div class="st-event-card" data-type="<?php echo $is_virtual ? 'virtual' : 'campus'; ?>">
                <div class="st-event-date-badge">
                    <div class="month"><?php echo date('M', strtotime($ev_date)); ?></div>
                    <div class="day"><?php echo date('d', strtotime($ev_date)); ?></div>
                    <div class="year"><?php echo date('Y', strtotime($ev_date)); ?></div>
                </div>
                <div class="st-event-body">
                    <h3 style="margin:0 0 6px;font-size:17px;color:#1a1a2e;"><?php echo esc_html($ev_title); ?></h3>
                    <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:#6b7280;margin-bottom:8px;">
                        <?php if ($is_virtual): ?>
                            📹 <span><?php echo esc_html($ev_loc); ?></span>
                        <?php else: ?>
                            📍 <span><?php echo esc_html($ev_loc); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($ev_desc): ?>
                    <p style="font-size:14px;color:#374151;margin:0 0 16px;line-height:1.6;"><?php echo esc_html(substr($ev_desc, 0, 160)) . (strlen($ev_desc) > 160 ? '...' : ''); ?></p>
                    <?php endif; ?>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <button class="stitch-btn-primary" style="font-size:13px;padding:7px 16px;border-radius:8px;" onclick="stRSVP(this)">
                            📅 RSVP
                        </button>
                        <button class="stitch-btn-outline" style="font-size:13px;padding:7px 16px;border-radius:8px;" onclick="stSaveEvent(this)">
                            🔖 Save
                        </button>
                        <?php if ($is_virtual): ?>
                        <button class="stitch-btn-outline" style="font-size:13px;padding:7px 16px;border-radius:8px;color:#6b21a8;" onclick="alert('Link will be shared after RSVP.')">
                            🔗 Join Online
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div>
        <!-- My Schedule -->
        <div style="background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 4px rgba(13,39,165,.08);margin-bottom:16px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#6b21a8,#7c3aed);padding:16px 20px;color:#fff;">
                <h4 style="margin:0;font-size:15px;font-weight:700;">📅 My Schedule</h4>
                <p style="margin:4px 0 0;font-size:12px;opacity:.8;">You have <?php echo count($my_schedule); ?> events this week</p>
            </div>
            <?php foreach ($my_schedule as $sch): ?>
            <div style="padding:14px 20px;border-bottom:1px solid #f3f4f8;display:flex;gap:12px;align-items:center;">
                <div style="width:8px;height:8px;border-radius:50%;background:#6b21a8;flex-shrink:0;"></div>
                <div>
                    <div style="font-weight:600;font-size:13px;color:#1a1a2e;"><?php echo esc_html($sch['title']); ?></div>
                    <div style="font-size:11px;color:#6b7280;"><?php echo esc_html($sch['when']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            <div style="padding:12px 20px;border-bottom:1px solid #f3f4f8;">
                <a href="#" style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#6b21a8;text-decoration:none;">
                    📅 Active Schedule
                </a>
            </div>
            <div style="padding:12px 20px;border-bottom:1px solid #f3f4f8;">
                <a href="#" style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#6b7280;text-decoration:none;">
                    🕐 Past Events
                </a>
            </div>
            <div style="padding:12px 20px;border-bottom:1px solid #f3f4f8;">
                <a href="#" style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#6b7280;text-decoration:none;">
                    🔖 Saved Events
                </a>
            </div>
            <div style="padding:12px 20px;">
                <a href="#" style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#6b7280;text-decoration:none;">
                    ⚙️ Event Preferences
                </a>
            </div>
        </div>

        <!-- Member Perks -->
        <div style="background:linear-gradient(135deg,#f5f3ff,#f3e8ff);border-radius:12px;padding:20px;">
            <h4 style="margin:0 0 8px;font-size:14px;font-weight:700;color:#1a1a2e;">🎁 Member Perks</h4>
            <p style="font-size:13px;color:#374151;margin:0 0 12px;line-height:1.6;">Get exclusive access to early registration and member-only meetups.</p>
            <a href="?view=profile" class="stitch-btn-primary" style="font-size:13px;padding:8px 16px;border-radius:8px;text-decoration:none;">View My Benefits</a>
        </div>
    </div>
</div>

<script>
function stEventFilter(type, btn) {
    document.querySelectorAll('.st-events-type-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.st-event-card').forEach(c => {
        if (type === 'all' || c.dataset.type === type) {
            c.style.display = 'flex';
        } else {
            c.style.display = 'none';
        }
    });
}

function stRSVP(btn) {
    btn.textContent = '✓ RSVP\'d!';
    btn.style.background = '#2e7d32';
    btn.disabled = true;
}

function stSaveEvent(btn) {
    btn.textContent = '✓ Saved';
    btn.style.color = '#6b21a8';
    btn.disabled = true;
}
</script>
