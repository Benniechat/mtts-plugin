<!-- Left Sidebar Column: LinkedIn Style -->
<div class="lms-sidebar-card">
    <div style="text-align: center; padding: 20px 0 10px 0;">
        <img src="<?php echo MTTS_LMS_URL . 'assets/images/logo-mtts.jpg'; ?>" alt="MTTS Logo" style="height: 60px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
    </div>
    <div class="lms-side-user-info">
        <img src="<?php echo get_avatar_url( get_current_user_id() ); ?>" alt="Profile">
        <h3 style="font-size:16px; margin: 4px 0;"><?php echo esc_html( $student->matric_number ); ?></h3>
        <small style="color:var(--lms-text-sub); display:block; font-size:12px;"><?php echo \MttsLms\Core\Translator::trans('student_portal'); ?></small>
    </div>

    <ul class="lms-side-nav-list">
        <li><a href="?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>"><span class="dashicons dashicons-dashboard"></span> <?php echo \MttsLms\Core\Translator::trans('dashboard'); ?></a></li>
        <li><a href="?view=profile" class="<?php echo $view == 'profile' ? 'active' : ''; ?>"><span class="dashicons dashicons-admin-users"></span> <?php echo \MttsLms\Core\Translator::trans('my_profile'); ?></a></li>
        <li><a href="?view=courses" class="<?php echo $view == 'courses' ? 'active' : ''; ?>"><span class="dashicons dashicons-welcome-learn-more"></span> <?php echo \MttsLms\Core\Translator::trans('courses'); ?></a></li>
        <li><a href="?view=payments" class="<?php echo $view == 'payments' ? 'active' : ''; ?>"><span class="dashicons dashicons-cart"></span> <?php echo \MttsLms\Core\Translator::trans('payments'); ?></a></li>
        <li><a href="?view=exams" class="<?php echo $view == 'exams' ? 'active' : ''; ?>"><span class="dashicons dashicons-edit"></span> <?php echo \MttsLms\Core\Translator::trans('exams'); ?></a></li>
        <li><a href="?view=assignments" class="<?php echo $view == 'assignments' ? 'active' : ''; ?>"><span class="dashicons dashicons-clipboard"></span> <?php echo \MttsLms\Core\Translator::trans('assignments'); ?></a></li>
        <li><a href="?view=forum" class="<?php echo $view == 'forum' ? 'active' : ''; ?>"><span class="dashicons dashicons-format-chat"></span> Forum</a></li>
        <li><a href="?view=resources" class="<?php echo $view == 'resources' ? 'active' : ''; ?>"><span class="dashicons dashicons-book"></span> Resources</a></li>
        <li><a href="?view=badges" class="<?php echo $view == 'badges' ? 'active' : ''; ?>"><span class="dashicons dashicons-awards"></span> Badges</a></li>
        <li><a href="?view=portfolio" class="<?php echo $view == 'portfolio' ? 'active' : ''; ?>"><span class="dashicons dashicons-id"></span> Portfolio</a></li>
        <li><a href="?view=calendar" class="<?php echo $view == 'calendar' ? 'active' : ''; ?>"><span class="dashicons dashicons-calendar"></span> <?php echo \MttsLms\Core\Translator::trans('calendar'); ?></a></li>
        <li><a href="?view=change-password" class="<?php echo $view == 'change-password' ? 'active' : ''; ?>"><span class="dashicons dashicons-shield"></span> Security</a></li>
    </ul>
</div>

<!-- Community Card (Optional Bottom Link) -->
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

