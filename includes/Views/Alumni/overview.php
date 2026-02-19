<h2 style="margin-bottom: 20px;">Alumni Overview</h2>
<div class="mtts-dashboard-grid">
    <div class="mtts-card">
        <h3>Welcome Back!</h3>
        <p>It's great to see you again. Stay connected with your alma mater and fellow graduates.</p>
    </div>
    
    <div class="mtts-card">
        <h3>Network Stats</h3>
        <p class="mtts-stat-number"><?php echo count( get_users( array( 'role' => 'mtts_alumni' ) ) ); ?></p>
        <p style="color: var(--mtts-text-light);">Total Alumni</p>
    </div>

    <div class="mtts-card">
        <h3>Recent Events</h3>
        <p>No upcoming alumni events scheduled.</p>
    </div>
</div>
