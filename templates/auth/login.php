<div class="mtts-login-container">
    <form method="post" action="" class="mtts-form">
        <?php wp_nonce_field( 'mtts_login_action' ); ?>
        
        <?php if ( isset( $_GET['login_error'] ) ) : ?>
            <div class="mtts-alert mtts-alert-danger">
                Invalid username or password.
            </div>
        <?php endif; ?>

        <div class="mtts-form-group">
            <label for="mtts_username">Username or Email</label>
            <input type="text" name="mtts_username" id="mtts_username" required class="mtts-form-control">
        </div>

        <div class="mtts-form-group">
            <label for="mtts_password">Password</label>
            <input type="password" name="mtts_password" id="mtts_password" required class="mtts-form-control">
        </div>

        <div class="mtts-form-group mtts-checkbox">
            <label>
                <input type="checkbox" name="mtts_remember"> Remember Me
            </label>
        </div>

        <div class="mtts-form-group">
            <button type="submit" name="mtts_login_submit" class="mtts-btn mtts-btn-primary">Login</button>
        </div>
        
        <div class="mtts-login-footer">
            <a href="<?php echo wp_lostpassword_url(); ?>">Lost your password?</a>
        </div>
    </form>
</div>
