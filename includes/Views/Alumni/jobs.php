<style>
    .job-card {
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .job-info h3 {
        margin: 0 0 5px;
        color: var(--mtts-purple);
        font-size: 1.1rem;
    }
    .job-meta {
        font-size: 0.85rem;
        color: #777;
        display: flex;
        gap: 20px;
    }
    .job-badge {
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 15px;
        font-weight: 600;
    }
</style>

<div class="mtts-dashboard-section" style="max-width:900px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <h2 style="color:var(--mtts-purple); margin:0;">Ministry Opportunities</h2>
        <button class="mtts-btn" style="background:var(--mtts-gold); color:var(--mtts-purple); font-weight:bold; border-radius:15px; font-size:0.85rem;">Post an Opening</button>
    </div>

    <?php foreach ( $jobs as $job ) : ?>
        <div class="glass-card job-card">
            <div class="job-info">
                <h3><?php echo esc_html( $job->title ); ?></h3>
                <div class="job-meta">
                    <span><span class="dashicons dashicons-bank" style="font-size:0.9rem; margin-right:3px;"></span> <?php echo esc_html( $job->org ); ?></span>
                    <span><span class="dashicons dashicons-clock" style="font-size:0.9rem; margin-right:3px;"></span> <?php echo esc_html( $job->posted ); ?></span>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <span class="job-badge" style="background:#e0e7ff; color:#4338ca;"><?php echo esc_html( $job->type ); ?></span>
                <button class="mtts-btn mtts-btn-sm" style="background:var(--mtts-purple); color:#fff; border-radius:15px; padding:6px 20px;">Discern More</button>
            </div>
        </div>
    <?php endforeach; ?>

    <div style="margin-top:40px; text-align:center; background:rgba(255,215,0,0.1); border:1px dashed var(--mtts-gold); padding:20px; border-radius:16px;">
        <p style="color:var(--mtts-purple); font-weight:600;">Seeking a new mission field? Ensure your Ministry Pedigree is up to date.</p>
        <a href="?view=profile" class="mtts-btn mtts-btn-sm" style="background:var(--mtts-purple); color:#fff; margin-top:10px; border-radius:15px;">Update Profile</a>
    </div>
</div>
