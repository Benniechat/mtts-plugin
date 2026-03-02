<div class="wrap">
    <h1>Alumni Dashboard</h1>
    <div class="mtts-admin-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <div class="card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Total Alumni</h3>
            <p style="font-size: 24px; font-weight: bold; color: #7c3aed;">
                <?php 
                $alumni_count = count( get_users( array( 'role' => 'mtts_alumni' ) ) );
                echo esc_html( $alumni_count );
                ?>
            </p>
        </div>
        <div class="card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Recent Posts (24h)</h3>
            <p style="font-size: 24px; font-weight: bold; color: #10b981;">0</p>
        </div>
        <div class="card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Pending Members</h3>
            <p style="font-size: 24px; font-weight: bold; color: #f59e0b;">0</p>
        </div>
        <?php if ( class_exists('PeepSo') ) : ?>
        <div class="card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>PeepSo Activity</h3>
            <p style="font-size: 24px; font-weight: bold; color: #3b82f6;">
                <?php 
                global $wpdb;
                $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}peepso_activities");
                echo esc_html( $count ?: 0 );
                ?>
            </p>
        </div>
        <?php endif; ?>
        <?php if ( function_exists('bbp_get_statistics') ) : ?>
        <div class="card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Forum Topics</h3>
            <p style="font-size: 24px; font-weight: bold; color: #10b981;">
                <?php 
                $stats = bbp_get_statistics();
                echo esc_html( $stats['topic_count'] ?: 0 );
                ?>
            </p>
        </div>
        <?php endif; ?>
    </div>

    <h2 style="margin-top: 40px;">System Health</h2>
    <div style="background: #fff; padding: 20px; border-radius: 8px;">
        <p>Alumni moderation is <strong>Enabled</strong>.</p>
        <p>Auto-join for graduated students is <strong>Disabled</strong>.</p>
    </div>
</div>
