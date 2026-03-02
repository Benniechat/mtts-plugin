<div class="wrap">
    <h1>Alumni Directory</h1>
    <p>Manage all registered alumni profiles.</p>

    <div id="mtts-form-container" style="display: none; margin-bottom: 20px; border: 1px solid #ccc; padding: 15px; background: #fff;">
        <h2 id="form-title">Edit Alumni Profile</h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="alumni-form">
            <input type="hidden" name="action" value="mtts_save_alumni">
            <input type="hidden" name="user_id" id="user-id" value="0">
            <input type="hidden" name="student_id" id="student-id" value="0">
            <?php wp_nonce_field( 'mtts_save_alumni' ); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="display_name">Full Name</label></th>
                    <td><input type="text" name="display_name" id="display_name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="user_email">Email Address</label></th>
                    <td><input type="email" name="user_email" id="user_email" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="current_level">Final Level</label></th>
                    <td>
                        <select name="current_level" id="current_level">
                            <option value="400">400</option>
                            <option value="500">500</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="graduation_year">Graduation Year</label></th>
                    <td><input type="number" name="graduation_year" id="graduation_year" min="1990" max="<?php echo date('Y'); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th><label for="occupation">Current Occupation</label></th>
                    <td><input type="text" name="occupation" id="occupation" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="bio">Short Bio</label></th>
                    <td><textarea name="bio" id="bio" rows="4" class="large-text"></textarea></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" value="Update Alumni">
                <button type="button" class="button" onclick="resetAlumniForm()">Cancel</button>
            </p>
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Academic Level</th>
                <th>Graduation Year</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $alumni_users = get_users( array( 'role' => 'mtts_alumni' ) );
            if ( ! empty( $alumni_users ) ) :
                foreach ( $alumni_users as $user ) :
                    $student = \MttsLms\Models\Student::get_by_user( $user->ID );
                    $profile = \MttsLms\Models\AlumniProfile::get_by_user( $user->ID );
                    
                    // Prepare data for JS
                    $data = array(
                        'user_id'         => $user->ID,
                        'display_name'    => $user->display_name,
                        'user_email'      => $user->user_email,
                        'student_id'      => $student ? $student->id : 0,
                        'current_level'   => $student ? $student->current_level : 400,
                        'graduation_year' => $profile->graduation_year ?? ($student->admission_year ?? date('Y')) + 4,
                        'occupation'      => $profile->occupation ?? $profile->current_ministry ?? '',
                        'bio'             => $profile ? $profile->bio : ''
                    );
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $user->display_name ); ?></strong></td>
                        <td><?php echo esc_html( $user->user_email ); ?></td>
                        <td><?php echo $student ? esc_html( $student->current_level ) : 'N/A'; ?></td>
                        <td><?php echo $data['graduation_year']; ?></td>
                        <td><span class="badge" style="background: #10b981; color: #fff; padding: 2px 8px; border-radius: 4px;">Active</span></td>
                        <td>
                            <a href="#" class="button button-small" onclick="editAlumni(<?php echo htmlspecialchars(json_encode($data)); ?>); return false;">Edit</a>
                            <?php if ( get_option('mtts_alumni_peepso_sync') && class_exists('PeepSo') ) : 
                                $peepso_url = PeepSo::get_instance()->get_user($user->ID)->get_profile_url();
                                ?>
                                <a href="<?php echo esc_url($peepso_url); ?>" class="button button-small" target="_blank" title="PeepSo Profile"><span class="dashicons dashicons-share-alt2" style="font-size:14px; margin-top:4px;"></span></a>
                            <?php endif; ?>

                            <?php if ( get_option('mtts_alumni_bbpress_sync') && function_exists('bbp_get_user_profile_url') ) : ?>
                                <a href="<?php echo esc_url(bbp_get_user_profile_url($user->ID)); ?>" class="button button-small" target="_blank" title="BBPress Profile"><span class="dashicons dashicons-admin-comments" style="font-size:14px; margin-top:4px;"></span></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else :
                ?>
                <tr><td colspan="6">No alumni found.</td></tr>
                <?php
            endif;
            ?>
        </tbody>
    </table>
</div>

<script>
function editAlumni(data) {
    document.getElementById('mtts-form-container').style.display = 'block';
    document.getElementById('user-id').value = data.user_id;
    document.getElementById('student-id').value = data.student_id;
    document.getElementById('display_name').value = data.display_name;
    document.getElementById('user_email').value = data.user_email;
    document.getElementById('current_level').value = data.current_level;
    document.getElementById('graduation_year').value = data.graduation_year;
    document.getElementById('occupation').value = data.occupation;
    document.getElementById('bio').value = data.bio;
    window.scrollTo(0, 0);
}

function resetAlumniForm() {
    document.getElementById('alumni-form').reset();
    document.getElementById('user-id').value = '0';
    document.getElementById('mtts-form-container').style.display = 'none';
}
</script>
