<button class="mtts-mobile-toggle">
    <span class="dashicons dashicons-menu"></span> Menu
</button>
<div class="mtts-sidebar">
    <button class="mtts-sidebar-close">&times;</button>

    <div class="mtts-lecturer-info">
        <div class="mtts-avatar">
            <img src="<?php echo get_avatar_url( get_current_user_id() ); ?>" alt="Profile">
        </div>
        <h4><?php echo esc_html( wp_get_current_user()->display_name ); ?></h4>
        <small>Lecturer</small>
    </div>
    <nav class="mtts-nav">
        <ul>
            <li><a href="?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>"><?php echo \MttsLms\Core\Translator::trans('dashboard'); ?></a></li>
            <li><a href="?view=classes" class="<?php echo $view == 'classes' ? 'active' : ''; ?>"><?php echo \MttsLms\Core\Translator::trans('courses'); ?></a></li>
            <li><a href="?view=assignments" class="<?php echo $view == 'assignments' ? 'active' : ''; ?>"><?php echo \MttsLms\Core\Translator::trans('assignments'); ?></a></li>
            <li><a href="?view=students" class="<?php echo $view == 'students' ? 'active' : ''; ?>">Students</a></li>
            <li><a href="?view=events" class="<?php echo $view == 'events' ? 'active' : ''; ?>">Virtual Classroom</a></li>
            <li><a href="?view=questions" class="<?php echo $view == 'questions' ? 'active' : ''; ?>">Question Bank</a></li>
            <li><a href="?view=attendance" class="<?php echo $view == 'attendance' ? 'active' : ''; ?>">Attendance</a></li>
            <li><a href="?view=resources" class="<?php echo $view == 'resources' ? 'active' : ''; ?>"><span class="dashicons dashicons-book"></span> Resources</a></li>
            <li>
                <a href="?view=inbox" class="<?php echo $view == 'inbox' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-email"></span> Inbox
                    <?php
                    $unread_count = \MttsLms\Models\Message::count_unread( get_current_user_id() );
                    if ( $unread_count > 0 ) :
                    ?>
                        <span style="background:#e53e3e; color:#fff; border-radius:50%; padding:1px 6px; font-size:0.75rem; margin-left:4px;"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="<?php echo wp_logout_url( home_url() ); ?>"><?php echo \MttsLms\Core\Translator::trans('logout'); ?></a></li>
        </ul>
    </nav>
    <div class="mtts-lang-switch" style="text-align: center; margin-top: 20px;">
        <a href="?lang=en">EN</a> | <a href="?lang=fr">FR</a> | <a href="?lang=yo">YO</a>
    </div>
</div>
