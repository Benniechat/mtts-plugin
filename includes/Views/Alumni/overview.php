<h2 style="margin-bottom: 20px;">Alumni Overview</h2>
<div class="mtts-dashboard-grid">
    <div class="mtts-card">
        <h3>Welcome Back!</h3>
        <p>It's great to see you again. Stay connected with your alma mater and fellow graduates.</p>
    </div>
    
    <div class="mtts-card">
        <h3>Network Stats</h3>
        <p class="mtts-stat-number"><?php echo count( get_users( array( 'role' => 'mtts_alumni' ) ) ); ?></p>
        <p style="color: var(--mtts-text-light);">Total Alumni</p>
    </div>

    <div class="mtts-card">
        <h3>Recent Events</h3>
        <p>No upcoming alumni events scheduled.</p>
    </div>

    <?php if ( get_option('mtts_alumni_peepso_sync') && class_exists('PeepSo') ) : ?>
    <div class="mtts-card">
        <h3>Social Activity</h3>
        <?php echo do_shortcode('[peepso_activity profile="0" limit="5"]'); ?>
        <p><a href="<?php echo PeepSo::get_instance()->get_user($user->ID)->get_profile_url(); ?>">View full social profile &rarr;</a></p>
    </div>
    <?php endif; ?>

    <?php if ( get_option('mtts_alumni_bbpress_sync') && function_exists('bbp_get_user_profile_url') ) : ?>
    <div class="mtts-card">
        <h3>Forum Discussions</h3>
        <?php 
        // bbPress doesn't have a simple shortcode for user topics, so we'll show a link or custom loop
        if ( function_exists('bbp_has_topics') && bbp_has_topics( array( 'author' => $user->ID, 'posts_per_page' => 5 ) ) ) : 
            echo '<ul>';
            while ( bbp_topics() ) : bbp_the_topic();
                echo '<li><a href="' . bbp_get_topic_permalink() . '">' . bbp_get_topic_title() . '</a></li>';
            endwhile;
            echo '</ul>';
        else :
            echo '<p>No forum topics started yet.</p>';
        endif;
        ?>
        <p><a href="<?php echo bbp_get_user_profile_url( $user->ID ); ?>">View forum profile &rarr;</a></p>
    </div>
    <?php endif; ?>
</div>
