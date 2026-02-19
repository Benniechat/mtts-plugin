<?php
/**
 * Admin Stakeholder & Alumni Registration
 */
?>
<div class="wrap">
    <h1>👤 Stakeholder & Past Student Registration</h1>
    <p>Manually register alumni, partners, and external stakeholders to the MTTS Network.</p>

    <div class="mtts-card" style="max-width: 600px; margin-top: 20px; background:#fff; padding:30px; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
        <form method="post" action="">
            <?php wp_nonce_field( 'mtts_admin_stakeholder' ); ?>
            <input type="hidden" name="mtts_action" value="register_stakeholder">

            <div class="mtts-form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="mtts-form-control" required>
            </div>

            <div class="mtts-form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="mtts-form-control" required>
            </div>

            <div class="mtts-form-group">
                <label>User Role</label>
                <select name="role" class="mtts-form-control">
                    <option value="mtts_alumni">Alumni / Past Student</option>
                    <option value="mtts_stakeholder">External Stakeholder / Partner</option>
                </select>
            </div>

            <div class="mtts-form-group">
                <label>Ministry / Affiliation</label>
                <input type="text" name="affiliation" class="mtts-form-control">
            </div>

            <div class="mtts-form-group">
                <label>Special Notes (Internal)</label>
                <textarea name="notes" class="mtts-form-control" rows="3"></textarea>
            </div>

            <div style="margin-top:20px;">
                <button type="submit" class="button button-primary button-large">Register & Send Welcome Email</button>
            </div>
        </form>
    </div>

    <h2 style="margin-top:40px;">Recently Registered</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Affiliation</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5">No external stakeholders registered yet via this panel.</td>
            </tr>
        </tbody>
    </table>
</div>
