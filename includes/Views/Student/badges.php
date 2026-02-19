<?php
/**
 * Student Badges View
 */
$user_id = get_current_user_id();
$earned_badges = \MttsLms\Models\Badge::get_user_badges( $user_id );
?>
<div class="mtts-dashboard-section">
    <h2>🏅 My Achievements</h2>
    <p>Earn badges by staying active, scoring high in exams, and participating in the forum!</p>

    <div class="mtts-card">
        <?php if ( empty( $earned_badges ) ) : ?>
            <div style="text-align:center; padding:40px; color:#999;">
                <div style="font-size:3rem; margin-bottom:15px;">🔒</div>
                <p>No badges earned yet. Start your journey today!</p>
            </div>
        <?php else : ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap:20px;">
                <?php foreach ( $earned_badges as $badge ) : ?>
                    <div style="text-align:center; padding:15px; border:1px solid #7c3aed; border-radius:12px; background:#f9f7ff;">
                        <div style="font-size:2.5rem; margin-bottom:10px;"><?php echo esc_html( $badge->icon ); ?></div>
                        <h4 style="margin:0 0 5px; font-size:0.95rem; color:#1a1a2e;"><?php echo esc_html( $badge->name ); ?></h4>
                        <small style="color:#666; font-size:0.75rem; display:block; line-height:1.2;"><?php echo esc_html( $badge->description ); ?></small>
                        <div style="margin-top:10px; font-size:0.7rem; color:#999;"><?php echo date( 'M j, Y', strtotime( $badge->awarded_at ) ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <h3 style="margin-top:40px;">Available Badges</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:15px;">
        <?php
        $all_badges = \MttsLms\Models\Badge::all();
        foreach ( $all_badges as $b ) :
            $is_earned = false;
            foreach ( $earned_badges as $eb ) {
                if ( $eb->id == $b->id ) { $is_earned = true; break; }
            }
        ?>
            <div style="display:flex; align-items:center; gap:15px; padding:15px; background:#fff; border-radius:10px; border:1px solid #eee; opacity: <?php echo $is_earned ? '1' : '0.5'; ?>;">
                <div style="font-size:2rem;"><?php echo $is_earned ? esc_html( $b->icon ) : '🔒'; ?></div>
                <div>
                    <h5 style="margin:0;"><?php echo esc_html( $b->name ); ?></h5>
                    <p style="margin:0; font-size:0.8rem; color:#666;"><?php echo esc_html( $b->description ); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
