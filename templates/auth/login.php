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
        color: #144bb8;
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
        border-color: #144bb8;
        outline: none;
        box-shadow: 0 0 0 3px rgba(20, 75, 184, 0.1);
    }
    .mtts-btn-primary {
        background: #144bb8;
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
        background: #0d3a8e;
    }
    .mtts-footer-links {
        margin-top: 30px;
        text-align: center;
        font-size: 14px;
        color: #64748b;
    }
    .mtts-footer-links a {
        color: #144bb8;
        text-decoration: none;
        font-weight: 500;
    }
</style>

<div class="mtts-login-page">
    <div class="mtts-login-card">
        <div class="mtts-login-header">
            <img src="<?php echo MTTS_LMS_URL . 'assets/images/logo.png'; ?>" alt="MTTS Logo" style="height: 60px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;">
            <h1>MTT Seminary LMS</h1>
            <p>Welcome Back. Please enter your credentials.</p>
        </div>

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
                <a href="<?php echo wp_lostpassword_url(); ?>" style="color: #144bb8; text-decoration: none;">Forgot password?</a>
            </div>

            <button type="submit" name="mtts_login_submit" class="mtts-btn-primary">
                Login to Portal <span class="dashicons dashicons-arrow-right-alt"></span>
            </button>
        </form>

        <div class="mtts-footer-links">
            <p>© 2024 Mountain-Top Theological Seminary. All rights reserved.</p>
            <p><a href="https://mttseminary.org">mttseminary.org</a> | <a href="mailto:benniechatsystems@gmail.com">Support</a></p>
        </div>
    </div>
</div>
