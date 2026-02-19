<div class="wrap">
    <h1 class="wp-heading-inline">Reports & Analytics</h1>
    <hr class="wp-header-end">

    <div class="mtts-dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
        <div class="card" style="text-align: center; padding: 20px;">
            <h2><?php echo intval( $total_students ); ?></h2>
            <p>Total Students</p>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <h2><?php echo intval( $total_applications ); ?></h2>
            <p>Total Applications</p>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <h2><?php echo intval( $pending_applications ); ?></h2>
            <p>Pending Applications</p>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <h2>₦<?php echo number_format( floatval( $total_revenue ), 2 ); ?></h2>
            <p>Total Revenue (Paid)</p>
        </div>
    </div>

    <div style="margin-top: 40px;">
        <h2>Export Data</h2>
        <div class="card" style="padding: 20px;">
            <h3>Student Data</h3>
            <p>Export all student records to CSV.</p>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="mtts_export_students">
                <?php wp_nonce_field( 'mtts_export_students' ); ?>
                <button type="submit" class="button button-primary">Export CSV</button>
            </form>
        </div>
    </div>
</div>
