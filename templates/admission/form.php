<div class="mtts-admission-container">
    <h2>Apply for Admission - <?php echo esc_html($session->name); ?></h2>
    
    <?php if ( isset( $_GET['status'] ) && $_GET['status'] == 'success' ) : ?>
        <div class="mtts-alert mtts-alert-success">
            Application submitted successfully! Your ministerial journey with MTTS has officially begun.
        </div>
    <?php elseif ( isset( $_GET['status'] ) && $_GET['status'] == 'pending_payment' ) : ?>
        <div class="mtts-alert mtts-alert-info">
            <span class="dashicons dashicons-cart"></span> 
            Application saved! Redirecting to <strong><?php echo esc_html( ucfirst( $_GET['gateway'] ?? 'gateway' ) ); ?></strong> for payment processing...
        </div>
    <?php elseif ( isset( $_GET['status'] ) && $_GET['status'] == 'payment_error' ) : ?>
        <div class="mtts-alert mtts-alert-error">
            <span class="dashicons dashicons-warning"></span> 
            Payment Initialization Failed: <?php echo esc_html( $_GET['msg'] ?? 'Unknown gateway error.' ); ?>
            <p style="margin-top:10px;"><a href="<?php echo remove_query_arg(['status', 'msg']); ?>" class="mtts-btn mtts-btn-sm" style="background:#fff; color:#e11d48;">Try Again</a></p>
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
    <script>
    jQuery(document).ready(function($) {
        $('.mtts-form').on('submit', function() {
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Propagating Application...');
        });
    });
    </script>
    <style>
    .spin { animation: mtts-spin 2s linear infinite; }
    @keyframes mtts-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    <?php endif; ?>
</div>
