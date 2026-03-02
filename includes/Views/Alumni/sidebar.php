<div class="mtts-sidebar">
    <?php 
    $user_id = get_current_user_id();
    $profile = \MttsLms\Models\AlumniProfile::get_by_user( $user_id );
    $avatar_url = $profile->profile_picture_url ?: get_avatar_url( $user_id );
    ?>
    <div class="mtts-student-info">
        <img src="<?php echo $avatar_url; ?>" class="mtts-user-avatar" alt="Profile" style="object-fit:cover;">
        <h3 style="color: white; margin: 10px 0; font-size: 1.1rem;"><?php echo esc_html( wp_get_current_user()->display_name ); ?></h3>
        <span class="mtts-status mtts-status-success">Community Member</span>
    </div>

    <nav class="mtts-nav">
        <ul>
            <li><a href="?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="?view=feed" class="<?php echo $view == 'feed' ? 'active' : ''; ?>">Covenant Feed</a></li>
            <?php if ( get_option('mtts_alumni_peepso_sync') && class_exists('PeepSo') ) : ?>
                <li><a href="<?php echo PeepSo::get_instance()->get_user(get_current_user_id())->get_profile_url(); ?>" target="_blank">Messenger</a></li>
            <?php endif; ?>
            <?php if ( get_option('mtts_alumni_bbpress_sync') && function_exists('bbp_get_user_profile_url') ) : ?>
                <li><a href="<?php echo bbp_get_user_profile_url(get_current_user_id()); ?>" target="_blank">Forum Activity</a></li>
            <?php endif; ?>
            <li><a href="?view=directory" class="<?php echo $view == 'directory' ? 'active' : ''; ?>">Covenant Directory</a></li>
            <li><a href="?view=messenger" class="<?php echo $view == 'messenger' ? 'active' : ''; ?>">Propagate Messenger</a></li>
            <li><a href="?view=groups" class="<?php echo $view == 'groups' ? 'active' : ''; ?>">Ministry Circles</a></li>
            <li><a href="?view=friends" class="<?php echo $view == 'friends' ? 'active' : ''; ?>">Fellowship Circle</a></li>
            <li><a href="?view=events" class="<?php echo $view == 'events' ? 'active' : ''; ?>">Events</a></li>
            <li><a href="?view=jobs" class="<?php echo $view == 'jobs' ? 'active' : ''; ?>">Ministry Jobs</a></li>
            <li><a href="?view=profile" class="<?php echo $view == 'profile' ? 'active' : ''; ?>">My Profile</a></li>
            <li><a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a></li>
        </ul>
    </nav>
</div>
