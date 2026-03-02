<!-- Left Sidebar Column: LinkedIn Style -->
<div class="lms-sidebar-card">
    <div style="text-align: center; padding: 20px 0 10px 0;">
        <img src="<?php echo MTTS_LMS_URL . 'assets/images/logo-mtts.jpg'; ?>" alt="MTTS Logo" style="height: 60px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
    </div>
    <div class="lms-side-user-info">
        <img src="<?php echo get_avatar_url( get_current_user_id() ); ?>" alt="Profile">
        <h3 style="font-size:16px; margin: 4px 0;"><?php echo \MttsLms\Core\Translator::trans('lecturer_portal'); ?></h3>
        <small style="color:var(--lms-text-sub); display:block; font-size:12px;"><?php echo esc_html( wp_get_current_user()->display_name ); ?></small>
    </div>

    <ul class="lms-side-nav-list">
        <li><a href="?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>"><span class="dashicons dashicons-dashboard"></span> <?php echo \MttsLms\Core\Translator::trans('dashboard'); ?></a></li>
        <li><a href="?view=classes" class="<?php echo $view == 'classes' ? 'active' : ''; ?>"><span class="dashicons dashicons-welcome-learn-more"></span> <?php echo \MttsLms\Core\Translator::trans('courses'); ?></a></li>
        <li><a href="?view=assignments" class="<?php echo $view == 'assignments' ? 'active' : ''; ?>"><span class="dashicons dashicons-clipboard"></span> <?php echo \MttsLms\Core\Translator::trans('assignments'); ?></a></li>
        <li><a href="?view=students" class="<?php echo $view == 'students' ? 'active' : ''; ?>"><span class="dashicons dashicons-groups"></span> Students</a></li>
        <li><a href="?view=events" class="<?php echo $view == 'events' ? 'active' : ''; ?>"><span class="dashicons dashicons-video-alt3"></span> Virtual Classroom</a></li>
        <li><a href="?view=questions" class="<?php echo $view == 'questions' ? 'active' : ''; ?>"><span class="dashicons dashicons-database-add"></span> Question Bank</a></li>
        <li><a href="?view=attendance" class="<?php echo $view == 'attendance' ? 'active' : ''; ?>"><span class="dashicons dashicons-yes"></span> Attendance</a></li>
        <li><a href="?view=resources" class="<?php echo $view == 'resources' ? 'active' : ''; ?>"><span class="dashicons dashicons-book"></span> Resources</a></li>
        <li><a href="?view=change-password" class="<?php echo $view == 'change-password' ? 'active' : ''; ?>"><span class="dashicons dashicons-shield"></span> Security</a></li>
    </ul>
</div>

<!-- Community Card -->
<div class="lms-sidebar-card" style="margin-top: 12px;">
    <ul class="lms-side-nav-list">
        <li>
            <a href="<?php echo esc_url( home_url('/alumni-network') ); ?>" style="color:var(--lms-purple);">
                <span class="dashicons dashicons-groups"></span> Alumni Network
            </a>
        </li>
        <li>
            <a href="<?php echo wp_logout_url( home_url() ); ?>" style="color:var(--lms-danger);">
                <span class="dashicons dashicons-exit"></span> <?php echo \MttsLms\Core\Translator::trans('logout'); ?>
            </a>
        </li>
    </ul>
</div>

