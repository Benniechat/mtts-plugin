<div class="wrap">
    <h1>📈 Advanced Analytics Dashboard</h1>
    <p>Real-time insights across campus centers, academic performance, and enrollment trends.</p>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap:30px; margin-top:30px;">
        
        <!-- Enrollment by Level -->
        <div class="mtts-card" style="background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <h3>Enrollment by Level</h3>
            <canvas id="enrollmentChart"></canvas>
        </div>

        <!-- Grade Distribution -->
        <div class="mtts-card" style="background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <h3>Grade Distribution (Overall)</h3>
            <canvas id="gradeChart"></canvas>
        </div>

        <!-- Campus Center Stats -->
        <div class="mtts-card" style="background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <h3>Students per Campus</h3>
            <canvas id="campusChart"></canvas>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enrollment Chart
    const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
    new Chart(enrollmentCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode( array_column( $enrollment, 'current_level' ) ); ?>,
            datasets: [{
                label: 'Students',
                data: <?php echo json_encode( array_column( $enrollment, 'count' ) ); ?>,
                backgroundColor: '#7c3aed',
                borderRadius: 8
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Grade Chart
    const gradeCtx = document.getElementById('gradeChart').getContext('2d');
    new Chart(gradeCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode( array_column( $grades, 'grade' ) ); ?>,
            datasets: [{
                data: <?php echo json_encode( array_column( $grades, 'count' ) ); ?>,
                backgroundColor: ['#22c55e', '#3b82f6', '#eab308', '#f97316', '#ef4444']
            }]
        }
    });

    // Campus Chart
    const campusCtx = document.getElementById('campusChart').getContext('2d');
    new Chart(campusCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode( array_column( $campus, 'name' ) ); ?>,
            datasets: [{
                data: <?php echo json_encode( array_column( $campus, 'count' ) ); ?>,
                backgroundColor: ['#6366f1', '#a855f7', '#ec4899', '#f43f5e']
            }]
        }
    });
});
</script>
