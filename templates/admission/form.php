<div class="mtts-admission-container">
    <h2>Apply for Admission - <?php echo esc_html($session->name); ?></h2>
    
    <?php if ( isset( $_GET['status'] ) && $_GET['status'] == 'success' ) : ?>
        <div class="mtts-alert mtts-alert-success">
            Application submitted successfully! Please check your email for further instructions.
        </div>
    <?php else: ?>

    <form method="post" action="" enctype="multipart/form-data" class="mtts-form">
        <?php wp_nonce_field( 'mtts_admission_action' ); ?>
        
        <h3>Personal Information</h3>
        <div class="mtts-form-group">
            <label for="applicant_name">Full Name</label>
            <input type="text" name="applicant_name" id="applicant_name" required class="mtts-form-control">
        </div>

        <div class="mtts-form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required class="mtts-form-control">
        </div>

        <div class="mtts-form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" required class="mtts-form-control">
        </div>
        
        <div class="mtts-form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob" required class="mtts-form-control">
        </div>

        <div class="mtts-form-group">
            <label for="gender">Gender</label>
            <select name="gender" id="gender" class="mtts-form-control">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        
        <div class="mtts-form-group">
            <label for="address">Address</label>
            <textarea name="address" id="address" class="mtts-form-control"></textarea>
        </div>

        <div class="mtts-form-group">
            <label for="denomination">Denomination</label>
            <input type="text" name="denomination" id="denomination" class="mtts-form-control">
        </div>

        <h3>Academic Program</h3>
        <div class="mtts-form-group">
            <label for="program_id">Select Program</label>
            <select name="program_id" id="program_id" required class="mtts-form-control">
                <option value="">-- Select Program --</option>
                <?php foreach($programs as $prog): ?>
                    <option value="<?php echo esc_attr($prog->id); ?>"><?php echo esc_html($prog->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mtts-form-group">
            <label for="campus_center_id">Campus Center <span style="color:red;">*</span></label>
            <select name="campus_center_id" id="campus_center_id" required class="mtts-form-control">
                <option value="">-- Select Campus Center --</option>
                <?php if ( ! empty( $campus_centers ) ) : ?>
                    <?php foreach ( $campus_centers as $center ) : ?>
                        <option value="<?php echo esc_attr( $center->id ); ?>">
                            <?php echo esc_html( $center->name ); ?> (<?php echo esc_html( $center->code ); ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <small style="color:#666;">Your matric number will reflect your campus center. e.g. <strong>MTTS/<?php echo date('Y'); ?>/LAG/001</strong></small>
        </div>

        <h3>Uploads</h3>
        <div class="mtts-form-group">
            <label for="passport">Passport Photograph</label>
            <input type="file" name="passport" id="passport" accept="image/*" required class="mtts-form-control">
        </div>

        <div class="mtts-form-group">
            <label for="credentials">Credentials (PDF)</label>
            <input type="file" name="credentials" id="credentials" accept=".pdf" class="mtts-form-control">
        </div>

        <div class="mtts-form-group">
            <button type="submit" name="mtts_admission_submit" class="mtts-btn mtts-btn-primary">Submit Application</button>
        </div>
    </form>
    <?php endif; ?>
</div>
