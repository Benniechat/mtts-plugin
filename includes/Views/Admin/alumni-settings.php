<div class="wrap">
    <h1>Alumni System Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'mtts_alumni_settings' ); ?>
        <?php do_settings_sections( 'mtts-alumni-settings' ); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">Membership Access</th>
                <td>
                    <label>
                        <input type="checkbox" name="mtts_alumni_auto_join" value="1">
                        Automatically add graduating students to Alumni network
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">Social Feed Moderation</th>
                <td>
                    <select name="mtts_alumni_moderation">
                        <option value="none">No Moderation</option>
                        <option value="all">Moderate All Posts</option>
                        <option value="flags">Moderate Flagged Only</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">Profile Visibility</th>
                <td>
                    <label>
                        <input type="radio" name="mtts_alumni_visibility" value="public" <?php checked(get_option('mtts_alumni_visibility', 'public'), 'public'); ?>> Public (Logged-in users)
                    </label><br>
                    <label>
                        <input type="radio" name="mtts_alumni_visibility" value="private" <?php checked(get_option('mtts_alumni_visibility'), 'private'); ?>> Friends Only
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">BBPress Integration</th>
                <td>
                    <label>
                        <input type="checkbox" name="mtts_alumni_bbpress_sync" value="1" <?php checked(get_option('mtts_alumni_bbpress_sync')); ?>>
                        Show Forum activity in Alumni profile
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">PeepSo Integration</th>
                <td>
                    <label>
                        <input type="checkbox" name="mtts_alumni_peepso_sync" value="1" <?php checked(get_option('mtts_alumni_peepso_sync')); ?>>
                        Link Alumni profiles to PeepSo Social profiles
                    </label>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
</div>
