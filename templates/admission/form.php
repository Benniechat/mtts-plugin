<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');
    
    .mtts-admission-wrapper {
        font-family: 'Lexend', sans-serif;
        background: #f8fafc;
        min-height: 100vh;
        padding: 60px 20px;
    }
    .mtts-form-container {
        max-width: 900px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .mtts-form-header {
        background: #144bb8;
        padding: 48px;
        color: #ffffff;
        text-align: center;
    }
    .mtts-form-header h2 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #ffffff;
    }
    .mtts-form-header p {
        font-size: 18px;
        opacity: 0.9;
    }
    .mtts-form-body {
        padding: 48px;
    }
    .mtts-section-divider {
        margin: 40px 0 24px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .mtts-section-divider .dashicons {
        color: #144bb8;
    }
    .mtts-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
    .mtts-form-group {
        margin-bottom: 20px;
    }
    .mtts-form-group label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        color: #475569;
    }
    .mtts-form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-family: inherit;
        font-size: 15px;
        transition: all 0.3s;
    }
    .mtts-form-control:focus {
        border-color: #144bb8;
        outline: none;
        box-shadow: 0 0 0 3px rgba(20, 75, 184, 0.1);
    }
    .mtts-upload-box {
        border: 2px dashed #e2e8f0;
        padding: 32px;
        border-radius: 12px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s;
        cursor: pointer;
    }
    .mtts-upload-box:hover {
        border-color: #144bb8;
        background: #eff6ff;
    }
    .mtts-submit-btn {
        background: #144bb8;
        color: #ffffff;
        padding: 16px 48px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 16px;
        border: none;
        cursor: pointer;
        width: 100%;
        margin-top: 32px;
        transition: background 0.3s;
    }
    .mtts-submit-btn:hover {
        background: #0d3a8e;
    }
    .mtts-alert {
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }
    .mtts-alert-success { background: #ecfdf5; border: 1px solid #10b981; color: #065f46; }
</style>

<div class="mtts-admission-wrapper">
    <div class="mtts-form-container">
        <?php if ( isset( $_GET['status'] ) && $_GET['status'] == 'success' ) : ?>
            <div class="mtts-form-header" style="background: #10b981;">
                <span class="dashicons dashicons-yes-alt" style="font-size: 64px; width: 64px; height: 64px; margin-bottom: 20px;"></span>
                <h2>Application Submitted!</h2>
                <p>Your ministerial journey with MTTS has officially begun.</p>
            </div>
            <div class="mtts-form-body" style="text-align: center;">
                <h3 style="font-size: 24px; margin-bottom: 15px;">What's Next?</h3>
                <div style="max-width: 500px; margin: 0 auto; text-align: left;">
                    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                        <div style="width: 32px; height: 32px; background: #eff6ff; color: #144bb8; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 700;">1</div>
                        <div><strong>Application Review</strong><br><span style="color: #64748b; font-size: 14px;">Our Academic Board will review your credentials within 7 working days.</span></div>
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <div style="width: 32px; height: 32px; background: #eff6ff; color: #144bb8; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 700;">2</div>
                        <div><strong>Admission Letter</strong><br><span style="color: #64748b; font-size: 14px;">Keep an eye on your email for your official acceptance letter and fee payment link.</span></div>
                    </div>
                </div>
                <a href="<?php echo home_url(); ?>" class="mtts-submit-btn" style="display: inline-block; text-decoration: none; margin-top: 40px; width: auto;">Return Home</a>
            </div>
        <?php else: ?>
            <div class="mtts-form-header">
                <h2>Student Admission Form</h2>
                <p>Shape your calling. Join our community of scholars and spiritual leaders.</p>
            </div>
            
            <form method="post" action="" enctype="multipart/form-data" class="mtts-form mtts-form-body">
                <?php wp_nonce_field( 'mtts_admission_action' ); ?>
                
                <div class="mtts-section-divider"><span class="dashicons dashicons-admin-users"></span> Personal Information</div>
                <div class="mtts-form-grid">
                    <div class="mtts-form-group">
                        <label for="applicant_name">Full Name (Surname First)</label>
                        <input type="text" name="applicant_name" id="applicant_name" required class="mtts-form-control" placeholder="e.g. DOE, JOHN SAMUEL">
                    </div>
                    <div class="mtts-form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" required class="mtts-form-control" placeholder="personal@email.com">
                    </div>
                    <div class="mtts-form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" required class="mtts-form-control" placeholder="+234...">
                    </div>
                    <div class="mtts-form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" name="dob" id="dob" required class="mtts-form-control">
                    </div>
                </div>

                <div class="mtts-section-divider"><span class="dashicons dashicons-church"></span> Spiritual & Church Background</div>
                <div class="mtts-form-grid">
                    <div class="mtts-form-group" style="grid-column: span 2;">
                        <label for="denomination">Current Denomination / Local Church</label>
                        <input type="text" name="denomination" id="denomination" class="mtts-form-control" placeholder="e.g. MFM Worldwide">
                    </div>
                </div>

                <div class="mtts-section-divider"><span class="dashicons dashicons-welcome-learn-more"></span> Academic History & Program</div>
                <div class="mtts-form-grid">
                    <div class="mtts-form-group">
                        <label for="program_id">Selected Program</label>
                        <select name="program_id" id="program_id" required class="mtts-form-control">
                            <option value="">-- Choose Course --</option>
                            <?php foreach($programs as $prog): ?>
                                <option value="<?php echo esc_attr($prog->id); ?>"><?php echo esc_html($prog->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mtts-form-group">
                        <label for="campus_center_id">Preferred Campus Center</label>
                        <select name="campus_center_id" id="campus_center_id" required class="mtts-form-control">
                            <option value="">-- Select Center --</option>
                            <?php foreach($campus_centers as $center): ?>
                                <option value="<?php echo esc_attr($center->id); ?>"><?php echo esc_html($center->name); ?> (<?php echo esc_html($center->code); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mtts-section-divider"><span class="dashicons dashicons-cloud-upload"></span> Document Uploads</div>
                <div class="mtts-form-grid">
                    <div class="mtts-form-group">
                        <label for="passport">Passport Photograph (Max 2MB)</label>
                        <div class="mtts-upload-box" onclick="document.getElementById('passport').click()">
                            <span class="dashicons dashicons-camera" style="font-size: 32px; width: 32px; height: 32px; color: #94a3b8; margin-bottom: 8px;"></span>
                            <div style="font-size: 13px; color: #64748b;">JPG or PNG only</div>
                            <input type="file" name="passport" id="passport" accept="image/*" required style="display:none;">
                        </div>
                    </div>
                    <div class="mtts-form-group">
                        <label for="credentials">Academic Credentials (PDF)</label>
                        <div class="mtts-upload-box" onclick="document.getElementById('credentials').click()">
                            <span class="dashicons dashicons-pdf" style="font-size: 32px; width: 32px; height: 32px; color: #94a3b8; margin-bottom: 8px;"></span>
                            <div style="font-size: 13px; color: #64748b;">SSCE / Degree / Masters</div>
                            <input type="file" name="credentials" id="credentials" accept=".pdf" style="display:none;">
                        </div>
                    </div>
                </div>

                <div style="margin-top: 40px; padding: 20px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; font-size: 14px; color: #92400e;">
                    <span class="dashicons dashicons-shield" style="font-size: 16px; width: 16px; height: 16px;"></span> 
                    Mountain-Top Theological Seminary is an accredited institution dedicated to spiritual excellence. Your data is protected under our privacy policy.
                </div>

                <button type="submit" name="mtts_admission_submit" class="mtts-submit-btn">Propagate Application <span class="dashicons dashicons-arrow-right-alt"></span></button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('.mtts-form').on('submit', function() {
            const btn = $(this).find('.mtts-submit-btn');
            btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Processing Spiritual Journey...');
        });
        
        // Simple file name display
        $('input[type="file"]').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            if(fileName) {
                $(this).closest('.mtts-upload-box').find('div').text(fileName).css('color', '#144bb8');
            }
        });
    });
</script>

<style>
    .spin { animation: mtts-spin 2s linear infinite; display: inline-block; vertical-align: middle; }
    @keyframes mtts-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>
