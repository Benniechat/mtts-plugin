<div class="mtts-container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
    
    <div style="text-align: center; margin-bottom: 50px;">
        <h1 style="color: var(--mtts-primary); font-size: 2.5rem; margin-bottom: 10px;">Academic Programs & Courses</h1>
        <p style="color: var(--mtts-text-light); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Explore our comprehensive theological curriculum, designed to equip you for impactful ministry and leadership.</p>
    </div>

    <?php if ( ! empty( $catalog ) ) : ?>
        <?php foreach ( $catalog as $program_name => $courses ) : ?>
            
            <div class="mtts-program-section" style="margin-bottom: 60px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 15px;">
                    <h2 style="color: var(--mtts-primary-dark); margin: 0; font-size: 1.8rem;"><?php echo esc_html( $program_name ); ?></h2>
                    <span style="background: var(--mtts-accent); color: #000; padding: 5px 15px; border-radius: 20px; font-weight: 600; font-size: 0.9rem;"><?php echo count($courses); ?> Courses</span>
                </div>

                <div class="mtts-course-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                    <?php if ( ! empty( $courses ) ) : ?>
                        <?php foreach ( $courses as $course ) : ?>
                            <div class="mtts-card mtts-course-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease; border-top: 4px solid var(--mtts-primary);">
                                <div style="margin-bottom: 15px;">
                                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--mtts-primary-dark); background: rgba(75, 0, 130, 0.1); padding: 4px 8px; border-radius: 4px;"><?php echo esc_html( $course->course_code ); ?></span>
                                    <span style="float: right; font-size: 0.85rem; color: var(--mtts-text-light);"><?php echo esc_html( $course->credit_unit ); ?> Units</span>
                                </div>
                                <h3 style="font-size: 1.25rem; margin-bottom: 10px; min-height: 50px;"><?php echo esc_html( $course->course_title ); ?></h3>
                                <p style="color: var(--mtts-text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
                                    <?php echo esc_html( wp_trim_words( $course->description, 15 ) ); ?>
                                </p>
                                <a href="<?php echo home_url('/admission/'); ?>" class="mtts-btn mtts-btn-small" style="width: 100%; text-align: center; display: block;">Apply Now</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No courses listed for this program yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        <?php endforeach; ?>
    <?php else : ?>
        <p style="text-align: center; font-size: 1.2rem;">Our course catalog is currently being updated. Please check back soon.</p>
    <?php endif; ?>

</div>

<style>
    .mtts-course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
