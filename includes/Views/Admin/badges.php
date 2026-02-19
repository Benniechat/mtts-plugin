<?php
/**
 * Admin Badge Management View
 * Allows admin to view all badges, award manually, and see who has earned each badge.
 */

// Handle manual award
if ( isset( $_POST['mtts_award_badge'] ) && check_admin_referer( 'mtts_award_badge' ) ) {
    $user_id  = intval( $_POST['user_id'] );
    $badge_id = intval( $_POST['badge_id'] );
    if ( $user_id && $badge_id ) {
        \MttsLms\Models\Badge::award( $user_id, $badge_id );
        echo '<div class="notice notice-success is-dismissible"><p>Badge awarded successfully!</p></div>';
    }
}

$all_badges = \MttsLms\Models\Badge::all();
?>
<div class="wrap">
    <h1>🏅 Badge Management</h1>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:30px; margin-top:20px;">

        <!-- Badge List -->
        <div>
            <h2>All Badges</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Trigger</th>
                        <th>Threshold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $all_badges as $badge ) : ?>
                        <tr>
                            <td style="font-size:1.5rem;"><?php echo esc_html( $badge->icon ); ?></td>
                            <td>
                                <strong><?php echo esc_html( $badge->name ); ?></strong><br>
                                <small style="color:#666;"><?php echo esc_html( $badge->description ); ?></small>
                            </td>
                            <td><code><?php echo esc_html( $badge->trigger_event ); ?></code></td>
                            <td><?php echo esc_html( $badge->trigger_value ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Manual Award -->
        <div>
            <h2>Award Badge Manually</h2>
            <div style="background:#fff; padding:20px; border:1px solid #ddd; border-radius:8px;">
                <form method="post" action="">
                    <?php wp_nonce_field( 'mtts_award_badge' ); ?>
                    <table class="form-table">
                        <tr>
                            <th><label for="user_id">Student</label></th>
                            <td>
                                <select name="user_id" class="regular-text" required>
                                    <option value="">-- Select Student --</option>
                                    <?php
                                    $students = get_users( [ 'role' => 'mtts_student' ] );
                                    foreach ( $students as $u ) :
                                    ?>
                                        <option value="<?php echo $u->ID; ?>"><?php echo esc_html( $u->display_name ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="badge_id">Badge</label></th>
                            <td>
                                <select name="badge_id" class="regular-text" required>
                                    <option value="">-- Select Badge --</option>
                                    <?php foreach ( $all_badges as $badge ) : ?>
                                        <option value="<?php echo $badge->id; ?>"><?php echo esc_html( $badge->icon . ' ' . $badge->name ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button( '🏅 Award Badge', 'primary', 'mtts_award_badge' ); ?>
                </form>
            </div>

            <h2 style="margin-top:30px;">Recent Awards</h2>
            <?php
            global $wpdb;
            $recent = $wpdb->get_results(
                "SELECT ub.*, b.name, b.icon, u.display_name
                 FROM {$wpdb->prefix}mtts_user_badges ub
                 JOIN {$wpdb->prefix}mtts_badges b ON ub.badge_id = b.id
                 JOIN {$wpdb->users} u ON ub.user_id = u.ID
                 ORDER BY ub.awarded_at DESC LIMIT 10"
            );
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead><tr><th>Student</th><th>Badge</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ( $recent as $r ) : ?>
                        <tr>
                            <td><?php echo esc_html( $r->display_name ); ?></td>
                            <td><?php echo esc_html( $r->icon . ' ' . $r->name ); ?></td>
                            <td style="font-size:0.85rem; color:#999;"><?php echo date( 'M j, Y', strtotime( $r->awarded_at ) ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ( empty( $recent ) ) : ?>
                        <tr><td colspan="3">No badges awarded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
