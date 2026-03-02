<div class="mtts-portfolio-container" style="max-width:900px; margin:0 auto; font-family:'Inter', sans-serif; color:#1a1a2e; background:#fff; border-radius:15px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1);">
    
    <!-- Header/Banner -->
    <div style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); padding:60px 40px; text-align:center; color:#fff;">
        <div style="font-size:5rem; margin-bottom:15px;">🎓</div>
        <h1 style="margin:0; font-size:2.5rem;"><?php echo esc_html( $user->display_name ); ?></h1>
        <p style="font-size:1.1rem; opacity:0.9; margin:10px 0 0;">
            MTTS Student Portfolio · <strong><?php echo esc_html( $student->matric_number ); ?></strong>
        </p>
    </div>

    <div style="padding:40px; display:grid; grid-template-columns: 300px 1fr; gap:40px;">
        
        <!-- Sidebar -->
        <div>
            <div style="margin-bottom:30px;">
                <h3 style="border-bottom:2px solid #7c3aed; padding-bottom:10px; margin-bottom:15px;">Details</h3>
                <p><strong>Level:</strong> <?php echo esc_html( $student->current_level ); ?>L</p>
                <p><strong>Campus:</strong> <?php 
                    $campus = \MttsLms\Models\CampusCenter::find( $student->campus_center_id );
                    echo esc_html( $campus ? $campus->name : 'Main Campus' );
                ?></p>
                <p><strong>Status:</strong> <span style="background:#dcfce7; color:#15803d; padding:2px 8px; border-radius:12px; font-size:0.8rem;"><?php echo ucfirst($student->status); ?></span></p>
            </div>

            <div>
                <h3 style="border-bottom:2px solid #7c3aed; padding-bottom:10px; margin-bottom:15px;">Earned Badges</h3>
                <?php if ( empty( $badges ) ) : ?>
                    <p style="color:#999; font-style:italic;">No badges earned yet.</p>
                <?php else : ?>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <?php foreach ( $badges as $badge ) : ?>
                            <div style="text-align:center; padding:10px; border:1px solid #eee; border-radius:8px;" title="<?php echo esc_attr($badge->description); ?>">
                                <div style="font-size:2rem;"><?php echo esc_html( $badge->icon ); ?></div>
                                <div style="font-size:0.75rem; margin-top:5px;"><?php echo esc_html( $badge->name ); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div>
            <h3 style="border-bottom:2px solid #7c3aed; padding-bottom:10px; margin-bottom:20px;">Academic Profile</h3>
            <p style="line-height:1.6; color:#555;">
                Welcome to my academic portfolio at <?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?>. I am currently pursuing my theological education with a focus on leadership and ministry.
            </p>

            <h4 style="margin-top:30px;">Completed Courses</h4>
            <?php if ( empty( $courses ) ) : ?>
                <p style="color:#999;">Academic record pending summary.</p>
            <?php else : ?>
                <ul style="list-style:none; padding:0;">
                    <?php foreach ( $courses as $course ) : 
                        $c = \MttsLms\Models\Course::find( $course->course_id );
                        if (!$c) continue;
                    ?>
                        <li style="padding:12px 0; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                            <span><?php echo esc_html( $c->title ); ?> (<?php echo esc_html($c->code); ?>)</span>
                            <span style="font-size:0.85rem; color:#999;"><?php echo esc_html($c->credits); ?> Credits</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div style="margin-top:50px; text-align:center; padding-top:20px; border-top:1px solid #eee;">
                <p style="font-size:0.9rem; color:#999;">Official Student Portfolio of <?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?></p>
            </div>
        </div>

    </div>
</div>
