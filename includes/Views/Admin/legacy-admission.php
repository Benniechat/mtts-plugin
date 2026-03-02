<?php
/**
 * Legacy Student Onboarding View
 */
?>
<div class="wrap mtts-legacy-admission">
    <h1>🎓 Legacy Student Onboarding</h1>
    <p>Retroactively add students who were in the school before this LMS. Backdated matrics will be generated automatically.</p>

    <div class="mtts-card" style="max-width: 800px; margin-top: 20px; background:#fff; padding:40px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.05);">
        <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
            <?php wp_nonce_field( 'mtts_manual_legacy_admission' ); ?>
            <input type="hidden" name="action" value="mtts_manual_legacy_admission">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="mtts-form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="mtts-form-control" placeholder="e.g. John Doe" required>
                </div>
                <div class="mtts-form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="mtts-form-control" placeholder="student@example.com" required>
                </div>
                
                <div class="mtts-form-group">
                    <label>Admission Year (Backdate)</label>
                    <input type="number" name="admission_year" class="mtts-form-control" value="<?php echo date('Y'); ?>" min="1990" max="<?php echo date('Y'); ?>" required>
                    <small style="color:#64748b;">This will be used for Matric Number generation.</small>
                </div>

                <div class="mtts-form-group">
                    <label>Current Level</label>
                    <select name="current_level" class="mtts-form-control">
                        <option value="100">100 Level</option>
                        <option value="200">200 Level</option>
                        <option value="300">300 Level</option>
                        <option value="400">400 Level</option>
                        <option value="Alumni">Alumni</option>
                    </select>
                </div>

                <div class="mtts-form-group">
                    <label>Program</label>
                    <select name="program_id" class="mtts-form-control" required>
                        <?php foreach($programs as $prog): ?>
                            <option value="<?php echo esc_attr($prog->id); ?>"><?php echo esc_html($prog->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mtts-form-group">
                    <label>Campus Center</label>
                    <select name="campus_id" class="mtts-form-control" required>
                        <?php foreach($campuses as $campus): ?>
                            <option value="<?php echo esc_attr($campus->id); ?>"><?php echo esc_html($campus->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mtts-form-group">
                    <label>Current GPA (Last Semester)</label>
                    <input type="number" name="current_gpa" class="mtts-form-control" step="0.01" min="0" max="5" value="0.00">
                </div>

                <div class="mtts-form-group">
                    <label>Cumulative GPA (CGPA)</label>
                    <input type="number" name="cumulative_gpa" class="mtts-form-control" step="0.01" min="0" max="5" value="0.00">
                </div>
            </div>

            <div style="margin-top:30px; text-align:right;">
                <button type="submit" class="button button-primary button-large">Onboard Student & Generate Matric</button>
            </div>
        </form>
    </div>
</div>

<style>
.mtts-legacy-admission .mtts-form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-top: 5px;
}
.mtts-legacy-admission label {
    font-weight: 600;
    color: #475569;
}
</style>
