<div class="wrap">
    <h1>Application Details: <?php echo esc_html( $application->applicant_name ); ?></h1>
    
    <div class="mtts-application-details" style="display: flex; gap: 20px;">
        <div style="flex: 2;">
            <div class="card">
                <h2>Personal Information</h2>
                <table class="form-table">
                    <tr><th>Name:</th><td><?php echo esc_html( $application->applicant_name ); ?></td></tr>
                    <tr><th>Email:</th><td><?php echo esc_html( $application->email ); ?></td></tr>
                    <tr><th>Phone:</th><td><?php echo esc_html( $application->phone ); ?></td></tr>
                    <tr><th>Program:</th><td><?php echo esc_html( $program ? $program->name : 'N/A' ); ?></td></tr>
                    <tr><th>Session:</th><td><?php echo esc_html( $session ? $session->name : 'N/A' ); ?></td></tr>
                </table>

                <h3>Additional Data</h3>
                <ul>
                    <?php foreach($form_data as $key => $value): ?>
                        <?php if(!in_array($key, ['applicant_name', 'email', 'phone', 'program_id'])): ?>
                            <li><strong><?php echo esc_html(ucfirst($key)); ?>:</strong> <?php echo esc_html($value); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <div style="flex: 1;">
            <div class="card">
                <h2>Actions</h2>
                <p><strong>Current Status:</strong> <?php echo esc_html( ucfirst( $application->status ) ); ?></p>
                
                <?php if ( $application->status === 'pending' ) : ?>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <input type="hidden" name="action" value="mtts_process_application">
                    <input type="hidden" name="application_id" value="<?php echo esc_attr($application->id); ?>">
                    <?php wp_nonce_field( 'mtts_process_application' ); ?>
                    
                    <button type="submit" name="mtts_action" value="approve" class="button button-primary button-large" style="width:100%; margin-bottom: 10px;">Approve Admission</button>
                    <button type="submit" name="mtts_action" value="reject" class="button button-secondary button-large" style="width:100%;" onclick="return confirm('Are you sure?');">Reject Application</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
