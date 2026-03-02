<h2 style="margin-bottom: 20px;">My Profile</h2>
<div class="mtts-card">
    <div style="display: flex; gap: 30px; align-items: flex-start;">
        <div style="text-align: center;">
            <img src="<?php echo get_avatar_url( wp_get_current_user()->ID, array('size'=>150) ); ?>" class="mtts-user-avatar" style="width: 150px; height: 150px;">
            <p style="margin-top: 10px; font-weight: bold; font-size: 1.2rem;"><?php echo esc_html( $student->matric_number ); ?></p>
        </div>
        
        <div style="flex: 1; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            <div>
                <label style="color: var(--mtts-text-light); display: block; font-size: 0.9rem;">Username (Matric Number)</label>
                <div style="font-weight: 700; font-size: 1.1rem; color: var(--mtts-purple);"><?php echo esc_html( wp_get_current_user()->user_login ); ?></div>
                <small style="color: #64748b; font-size: 11px;">Username is fixed and linked to your matric number.</small>
            </div>
            <div>
                <label style="color: var(--mtts-text-light); display: block; font-size: 0.9rem;">Full Name</label>
                <div style="font-weight: 600; font-size: 1.1rem;"><?php echo esc_html( wp_get_current_user()->display_name ); ?></div>
            </div>
            <div>
                <label style="color: var(--mtts-text-light); display: block; font-size: 0.9rem;">Email Address</label>
                <div style="font-weight: 600;"><?php echo esc_html( wp_get_current_user()->user_email ); ?></div>
            </div>
            <div>
                <label style="color: var(--mtts-text-light); display: block; font-size: 0.9rem;">Phone Number</label>
                <div style="font-weight: 600;"><?php echo esc_html( $student->phone ); ?></div>
            </div>
            <div>
                <label style="color: var(--mtts-text-light); display: block; font-size: 0.9rem;">Gender</label>
                <div style="font-weight: 600;"><?php echo esc_html( $student->gender ); ?></div>
            </div>
            <div>
                <label style="color: var(--mtts-text-light); display: block; font-size: 0.9rem;">Date of Birth</label>
                <div style="font-weight: 600;"><?php echo esc_html( date('d M Y', strtotime($student->date_of_birth)) ); ?></div>
            </div>
            <div>
                <label style="color: var(--mtts-text-light); display: block; font-size: 0.9rem;">Admission Year</label>
                <div style="font-weight: 600;"><?php echo esc_html( $student->admission_year ); ?></div>
            </div>
            <div style="grid-column: span 2;">
                <label style="color: var(--mtts-text-light); display: block; font-size: 0.9rem;">Address</label>
                <div style="font-weight: 600;"><?php echo esc_html( $student->address ); ?></div>
            </div>
        </div>
    </div>
</div>
