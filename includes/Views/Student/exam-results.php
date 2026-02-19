<div class="mtts-card" style="text-align: center; padding: 50px;">
    <?php 
    $score = isset($_GET['score']) ? floatval($_GET['score']) : 0;
    // Determine message based on score (mock logic)
    $message = $score >= 10 ? "Excellent Work!" : "Good Attempt.";
    $color = $score >= 10 ? "var(--mtts-status-success)" : "var(--mtts-primary)";
    ?>
    
    <div style="font-size: 4rem; color: <?php echo $color; ?>; margin-bottom: 20px;">
        <!-- Simple checkmark or star could go here if using icons -->
        ★
    </div>

    <h2 style="font-size: 2rem; margin-bottom: 10px;">Exam Submitted!</h2>
    <p style="font-size: 1.2rem; color: var(--mtts-text-light);"><?php echo $message; ?></p>
    
    <div style="margin: 30px 0; padding: 20px; background: #f9f9f9; display: inline-block; border-radius: 12px; min-width: 200px;">
        <span style="display: block; font-size: 0.9rem; color: #777; text-transform: uppercase; letter-spacing: 1px;">Your Score</span>
        <span style="display: block; font-size: 2.5rem; font-weight: bold; color: var(--mtts-primary);"><?php echo $score; ?> Points</span>
    </div>

    <div>
        <a href="?view=overview" class="mtts-btn mtts-btn-primary">Return to Dashboard</a>
        <a href="?mtts_doc=transcript" target="_blank" class="mtts-btn mtts-btn-small" style="margin-left: 10px; background: transparent; color: var(--mtts-primary); border: 1px solid var(--mtts-primary);">View Transcript</a>
    </div>
</div>
