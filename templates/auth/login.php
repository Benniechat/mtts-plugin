<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');
    
    .mtts-login-page {
        min-height: 100vh;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Lexend', sans-serif;
        padding: 20px;
    }
    .mtts-login-card {
        background: #ffffff;
        width: 100%;
        max-width: 450px;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .mtts-login-header {
        text-align: center;
        margin-bottom: 35px;
    }
    .mtts-login-header h1 {
        background: linear-gradient(135deg, #6b21a8 0%, #ea580c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
        font-size: 28px;
        margin-bottom: 5px;
    }
    .mtts-login-header p {
        color: #64748b;
        font-size: 16px;
    }
    .mtts-form-group {
        margin-bottom: 20px;
    }
    .mtts-form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: #1e293b;
    }
    .mtts-input-with-icon {
        position: relative;
    }
    .mtts-input-with-icon .dashicons {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .mtts-form-control {
        width: 100%;
        padding: 12px 12px 12px 40px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-family: inherit;
        transition: all 0.3s;
    }
    .mtts-form-control:focus {
        border-color: #6b21a8;
        outline: none;
        box-shadow: 0 0 0 3px rgba(107, 33, 168, 0.1);
    }
    .mtts-btn-primary {
        background: #6b21a8;
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .mtts-btn-primary:hover {
        background: #581c87;
    }
    .mtts-footer-links {
        margin-top: 30px;
        text-align: center;
        font-size: 14px;
        color: #64748b;
    }
    .mtts-footer-links a {
        color: #6b21a8;
        text-decoration: none;
        font-weight: 500;
    }
    .mtts-login-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 30px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
    }
    .mtts-tab {
        flex: 1;
        padding: 10px;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.3s;
    }
    .mtts-tab.active {
        background: #ffffff;
        color: #6b21a8;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .mtts-tab-content {
        display: none;
    }
    .mtts-tab-content.active {
        display: block;
    }
</style>

<div class="mtts-login-page">
    <div class="mtts-login-card">
        <div class="mtts-login-header">
            <img src="<?php echo MTTS_LMS_URL . 'assets/images/logo.png'; ?>" alt="MTTS Logo" style="height: 60px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;">
            <h1>MTT Seminary LMS</h1>
            <p>Welcome Back. Please select your portal.</p>
        </div>

        <div class="mtts-login-tabs">
            <div class="mtts-tab active" onclick="switchMttsTab('institutional')">Student / Staff</div>
            <div class="mtts-tab" onclick="switchMttsTab('others')">Others Portal</div>
        </div>

        <div id="mtts-institutional" class="mtts-tab-content active">
            <form method="post" action="" class="mtts-form">
                <?php wp_nonce_field( 'mtts_login_action' ); ?>
                
                <?php if ( isset( $_GET['login_error'] ) ) : ?>
                    <div style="background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        Invalid credentials. Please check your matric number and try again.
                    </div>
                <?php endif; ?>

                <div class="mtts-form-group">
                    <label for="mtts_username">Matric Number or Email</label>
                    <div class="mtts-input-with-icon">
                        <span class="dashicons dashicons-admin-users"></span>
                        <input type="text" name="mtts_username" id="mtts_username" required class="mtts-form-control" placeholder="Enter identification">
                    </div>
                </div>

                <div class="mtts-form-group">
                    <label for="mtts_password">Password</label>
                    <div class="mtts-input-with-icon">
                        <span class="dashicons dashicons-lock"></span>
                        <input type="password" name="mtts_password" id="mtts_password" required class="mtts-form-control" placeholder="••••••••">
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; font-size: 14px;">
                    <label style="display:flex; align-items:center; gap:8px; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="mtts_remember"> Remember me
                    </label>
                    <a href="<?php echo wp_lostpassword_url(); ?>" style="color: #6b21a8; text-decoration: none;">Forgot password?</a>
                </div>

                <button type="submit" name="mtts_login_submit" class="mtts-btn-primary">
                    Institutional Login <span class="dashicons dashicons-arrow-right-alt"></span>
                </button>
            </form>
        </div>

        <div id="mtts-others" class="mtts-tab-content">
            <form method="post" action="" class="mtts-form">
                <?php wp_nonce_field( 'mtts_others_login_action' ); ?>
                
                <div class="mtts-form-group">
                    <label for="mtts_others_email">Email Address</label>
                    <div class="mtts-input-with-icon">
                        <span class="dashicons dashicons-email"></span>
                        <input type="email" name="mtts_username" id="mtts_others_email" required class="mtts-form-control" placeholder="alumni@email.com">
                    </div>
                </div>

                <div class="mtts-form-group">
                    <label for="mtts_others_password">Access Key / Password</label>
                    <div class="mtts-input-with-icon">
                        <span class="dashicons dashicons-shield"></span>
                        <input type="password" name="mtts_password" id="mtts_others_password" required class="mtts-form-control" placeholder="••••••••">
                    </div>
                </div>

                <div style="margin-bottom:25px;">
                    <p style="font-size: 13px; color: #64748b; margin: 0;">Access for Alumni, Visiting Scholars, and External Partners.</p>
                </div>

                <button type="submit" name="mtts_others_login_submit" class="mtts-btn-primary" style="background: #ea580c;">
                    Guest/Alumni Login <span class="dashicons dashicons-universal-access"></span>
                </button>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="#" style="color: #64748b; font-size: 13px; text-decoration: none;">Don't have access? <strong>Request Admission</strong></a>
                </div>
            </form>
        </div>

        <div class="mtts-footer-links">
            <p>© 2024 Mountain-Top Theological Seminary. All rights reserved.</p>
            <p><a href="https://mttseminary.org">mttseminary.org</a> | <a href="mailto:benniechatsystems@gmail.com">Support</a></p>
        </div>
    </div>
</div>

<script>
function switchMttsTab(tabName) {
    // Update tabs
    document.querySelectorAll('.mtts-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.currentTarget.classList.add('active');

    // Update content
    document.querySelectorAll('.mtts-tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById('mtts-' + tabName).classList.add('active');
}
</script>
