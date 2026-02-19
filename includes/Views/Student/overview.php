<h2 style="margin-bottom: 20px;">Dashboard Overview</h2>
<div class="mtts-dashboard-grid">
    <div class="mtts-card mtts-stat-card">
        <h3>Academic Level</h3>
        <p class="mtts-stat-number"><?php echo esc_html( $student->current_level ); ?></p>
        <p style="color: var(--mtts-text-light);"><?php echo \MttsLms\Models\Program::find($student->program_id)->code; ?></p>
    </div>
    
    <div class="mtts-card mtts-stat-card">
        <h3>Current Session</h3>
        <?php $session = \MttsLms\Models\Session::get_active_session(); ?>
        <?php if($session): ?>
            <p class="mtts-stat-number" style="font-size: 1.5rem;"><?php echo esc_html($session->name); ?></p>
            <p style="color: var(--mtts-status-success);">Active</p>
        <?php else: ?>
            <p>No active session</p>
        <?php endif; ?>
    </div>

    <!-- Real-time GPA/CGPA -->
    <div class="mtts-card mtts-stat-card" style="background: linear-gradient(135deg, rgba(75, 0, 130, 0.4), rgba(76, 58, 237, 0.4)); border: 1px solid rgba(255,255,255,0.1);">
        <h3>Current GPA</h3>
        <?php 
            $current_gpa = $session ? \MttsLms\Core\Grades::calculate_gpa( $student->id, $session->id ) : 0.00;
        ?>
        <p class="mtts-stat-number" style="color: #fff;"><?php echo number_format($current_gpa, 2); ?></p>
        <p style="font-size: 0.85em; opacity: 0.8;">Last exam performance</p>
    </div>

    <div class="mtts-card mtts-stat-card" style="background: linear-gradient(135deg, rgba(29, 78, 216, 0.4), rgba(30, 64, 175, 0.4)); border: 1px solid rgba(255,255,255,0.1);">
        <h3>CGPA</h3>
        <?php 
            $cgpa = \MttsLms\Core\Grades::calculate_cgpa( $student->id );
        ?>
        <p class="mtts-stat-number" style="color: #fff;"><?php echo number_format($cgpa, 2); ?></p>
        <p style="font-size: 0.85em; opacity: 0.8;"><?php echo \MttsLms\Core\Grades::get_class_of_degree($cgpa); ?></p>
    </div>

    <div class="mtts-card">
        <h3>Announcements</h3>
        <div style="border-left: 3px solid var(--mtts-accent); padding-left: 15px; margin-bottom: 10px;">
            <strong>Welcome to the new semester!</strong>
            <p>Registration ends on 30th September. Ensure you register your courses.</p>
        </div>
        <div style="border-left: 3px solid var(--mtts-primary); padding-left: 15px;">
            <strong>Exams Approaches</strong>
            <p>Check the timetable for the upcoming CBT exams.</p>
        </div>
    </div>
</div>
