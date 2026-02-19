<h2 style="margin-bottom: 20px;">My Profile</h2>
<div class="mtts-card">
    <form method="post" action="">
        <!-- Profile update form (placeholder) -->
        <p>Update your current job, location, and contact details here to help others find you.</p>
        
        <div class="mtts-form-group">
            <label>Current Ministry / Organization</label>
            <input type="text" class="mtts-form-control" name="organization" value="<?php echo esc_attr( get_user_meta( $user->ID, 'organization', true ) ); ?>">
        </div>

        <div class="mtts-form-group">
            <label>Graduation Year</label>
            <input type="number" class="mtts-form-control" name="grad_year" value="<?php echo esc_attr( get_user_meta( $user->ID, 'graduation_year', true ) ); ?>">
        </div>

        <button type="submit" class="mtts-btn mtts-btn-primary">Update Profile</button>
    </form>
</div>
