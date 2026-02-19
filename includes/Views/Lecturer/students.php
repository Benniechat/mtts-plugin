<?php
// students.php - For Lecturers
?>
<div class="mtts-dashboard-section">
    <h2>Student Management</h2>
    
    <div class="mtts-card">
        <form method="get" action="">
            <input type="hidden" name="view" value="students">
            <div class="mtts-form-group" style="display:flex; gap:10px; align-items:center;">
                <label>Select Course:</label>
                <select name="course_id" onchange="this.form.submit()" class="mtts-form-control">
                    <option value="">-- Choose Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?php echo $c->id; ?>" <?php selected($selected_course_id, $c->id); ?>>
                            <?php echo esc_html($c->course_code . ' - ' . $c->course_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if ( $selected_course_id && !empty($students) ) : ?>
        <div class="mtts-card" style="margin-top:20px;">
            <h3>Registered Students (<?php echo count($students); ?>)</h3>
            <table class="mtts-table-list">
                <thead>
                    <tr>
                        <th>Matric No</th>
                        <th>Name</th>
                        <th>Bonuses</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $students as $stu ) : 
                        $user = get_userdata( $stu->user_id );
                        $name = $user ? $user->display_name : 'Unknown';
                        $bonus = \MttsLms\Models\BonusMark::get_student_bonus( $stu->id, $selected_course_id );
                    ?>
                        <tr>
                            <td><?php echo esc_html( $stu->matric_number ); ?></td>
                            <td><?php echo esc_html( $name ); ?></td>
                            <td><?php echo number_format($bonus, 1); ?></td>
                            <td>
                                <button class="mtts-btn mtts-btn-sm" onclick="document.getElementById('bonus-form-<?php echo $stu->id; ?>').style.display='block'">Award Bonus</button>
                                
                                <div id="bonus-form-<?php echo $stu->id; ?>" style="display:none; position:absolute; background:white; border:1px solid #ccc; padding:15px; width:250px; z-index:100; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
                                    <h4>Award Bonus Mark</h4>
                                    <form method="post" action="">
                                        <input type="hidden" name="mtts_action" value="award_bonus">
                                        <input type="hidden" name="student_id" value="<?php echo $stu->id; ?>">
                                        <input type="hidden" name="course_id" value="<?php echo $selected_course_id; ?>">
                                        <?php wp_nonce_field( 'mtts_award_bonus' ); ?>
                                        
                                        <div class="mtts-form-group">
                                            <label>Marks (+/-)</label>
                                            <input type="number" name="marks" step="0.5" class="mtts-form-control" required>
                                        </div>
                                        <div class="mtts-form-group">
                                            <label>Reason</label>
                                            <input type="text" name="reason" class="mtts-form-control" placeholder="e.g. Active Participation">
                                        </div>
                                        <button type="submit" class="button button-small button-primary">Save</button>
                                        <button type="button" class="button button-small" onclick="this.closest('div').style.display='none'">Cancel</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ( $selected_course_id ) : ?>
        <div class="mtts-alert mtts-alert-info" style="margin-top:20px;">No students registered for this course yet.</div>
    <?php endif; ?>
</div>
