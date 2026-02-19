<button class="mtts-mobile-toggle">
    <span class="dashicons dashicons-menu"></span> Menu
</button>
<div class="mtts-sidebar">
    <button class="mtts-sidebar-close">&times;</button>

    <div class="mtts-student-info">
        <div class="mtts-avatar">
            <img src="<?php echo get_avatar_url( get_current_user_id() ); ?>" alt="Profile">
        </div>
        <h4><?php echo esc_html( $student->matric_number ); ?></h4>
    </div>
    <nav class="mtts-nav">
        <ul>
            <li><a href="?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>"><?php echo \MttsLms\Core\Translator::trans('dashboard'); ?></a></li>
            <li><a href="?view=profile" class="<?php echo $view == 'profile' ? 'active' : ''; ?>"><?php echo \MttsLms\Core\Translator::trans('my_profile'); ?></a></li>
            <li><a href="?view=courses" class="<?php echo $view == 'courses' ? 'active' : ''; ?>"><?php echo \MttsLms\Core\Translator::trans('courses'); ?></a></li>
            <li><a href="?view=payments" class="<?php echo $view == 'payments' ? 'active' : ''; ?>"><?php echo \MttsLms\Core\Translator::trans('payments'); ?></a></li>
            <li><a href="?view=exams" class="<?php echo $view == 'exams' ? 'active' : ''; ?>"><span class="dashicons dashicons-edit"></span> <?php echo \MttsLms\Core\Translator::trans('exams'); ?></a></li>
            <li><a href="?view=assignments" class="<?php echo $view == 'assignments' ? 'active' : ''; ?>"><span class="dashicons dashicons-clipboard"></span> <?php echo \MttsLms\Core\Translator::trans('assignments'); ?></a></li>
            <li><a href="?view=forum" class="<?php echo $view == 'forum' ? 'active' : ''; ?>"><span class="dashicons dashicons-format-chat"></span> Forum</a></li>
            <li><a href="?view=resources" class="<?php echo $view == 'resources' ? 'active' : ''; ?>"><span class="dashicons dashicons-book"></span> Resources</a></li>
            <li><a href="?view=badges" class="<?php echo $view == 'badges' ? 'active' : ''; ?>"><span class="dashicons dashicons-awards"></span> Badges</a></li>
            <li><a href="?view=portfolio" class="<?php echo $view == 'portfolio' ? 'active' : ''; ?>"><span class="dashicons dashicons-id"></span> My Portfolio</a></li>
            <li><a href="?view=calendar" class="<?php echo $view == 'calendar' ? 'active' : ''; ?>"><span class="dashicons dashicons-calendar"></span> <?php echo \MttsLms\Core\Translator::trans('calendar'); ?></a></li>
            <li><a href="?mtts_doc=admission_letter" target="_blank"><?php echo \MttsLms\Core\Translator::trans('admission_letter'); ?></a></li>
            <li><a href="?mtts_doc=id_card" target="_blank"><?php echo \MttsLms\Core\Translator::trans('id_card'); ?></a></li>
            <li><a href="?mtts_doc=transcript" target="_blank"><?php echo \MttsLms\Core\Translator::trans('transcript'); ?></a></li>
            <li>
                <a href="?view=inbox" class="<?php echo $view == 'inbox' ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-email"></span>
                    <?php echo \MttsLms\Core\Translator::trans('inbox'); ?>
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
