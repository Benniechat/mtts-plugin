<div class="mtts-sidebar">
    <div class="mtts-student-info">
        <img src="<?php echo get_avatar_url( get_current_user_id() ); ?>" class="mtts-user-avatar" alt="Profile">
        <h3 style="color: white; margin: 10px 0; font-size: 1.1rem;"><?php echo esc_html( wp_get_current_user()->display_name ); ?></h3>
        <span class="mtts-status mtts-status-success">Alumni</span>
    </div>

    <nav class="mtts-nav">
        <ul>
            <li><a href="?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="?view=feed" class="<?php echo $view == 'feed' ? 'active' : ''; ?>">Covenant Feed</a></li>
            <li><a href="?view=directory" class="<?php echo $view == 'directory' ? 'active' : ''; ?>">Alumni Directory</a></li>
            <li><a href="?view=friends" class="<?php echo $view == 'friends' ? 'active' : ''; ?>">Friends</a></li>
            <li><a href="?view=events" class="<?php echo $view == 'events' ? 'active' : ''; ?>">Events</a></li>
            <li><a href="?view=jobs" class="<?php echo $view == 'jobs' ? 'active' : ''; ?>">Ministry Jobs</a></li>
            <li><a href="?view=profile" class="<?php echo $view == 'profile' ? 'active' : ''; ?>">My Profile</a></li>
            <li><a href="?view=portfolio" class="<?php echo $view == 'portfolio' ? 'active' : ''; ?>">Public Portfolio</a></li>
            <li><a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a></li>
        </ul>
    </nav>
</div>
