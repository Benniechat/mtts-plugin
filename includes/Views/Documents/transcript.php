<!DOCTYPE html>
<html>
<head>
    <title>Academic Transcript - MTTS</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 40px; max-width: 800px; margin: 0 auto; }
        h1, h2, h3 { text-align: center; }
        .meta-table { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .meta-table td { padding: 5px; }
        .results-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .results-table th, .results-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .results-table th { background: #f0f0f0; }
        .session-header { background: #eee; font-weight: bold; text-align: center; padding: 5px; margin-top: 20px; border: 1px solid #000; border-bottom: none; }
    </style>
</head>
<body>
    <h1>Mountain-Top Theological Seminary</h1>
    <h3>Official Academic Transcript</h3>

    <table class="meta-table">
        <tr>
            <td><strong>Name:</strong> <?php echo esc_html( $user->display_name ); ?></td>
            <td><strong>Matric No:</strong> <?php echo esc_html( $student->matric_number ); ?></td>
        </tr>
        <tr>
            <td><strong>Program:</strong> <?php echo esc_html( $program->name ); ?></td>
            <td><strong>Date Issued:</strong> <?php echo date('d M Y'); ?></td>
        </tr>
    </table>

    <?php if ( ! empty( $transcript_data ) ) : ?>
        <?php foreach ( $transcript_data as $session_name => $courses ) : ?>
            
            <div class="session-header"><?php echo esc_html( $session_name ); ?> Academic Session</div>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Unit</th>
                        <th>Grade</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $total_units = 0;
                        $total_points = 0; 
                    ?>
                    <?php foreach ( $courses as $course ) : ?>
                        <?php 
                            // Calculate Grade/Points wrapper logic here 
                            // Assuming grade is stored or calc on fly
                            $grade = $course->grade ? $course->grade : 'N/A';
                            // Simple mock points for GPA
                            $points = 0;
                            if($grade == 'A') $points = 5;
                            elseif($grade == 'B') $points = 4;
                            elseif($grade == 'C') $points = 3;
                            elseif($grade == 'D') $points = 2;
                            elseif($grade == 'E') $points = 1;
                            
                            $wgp = $points * $course->credit_unit;
                            $total_units += $course->credit_unit;
                            $total_points += $wgp;
                        ?>
                        <tr>
                            <td><?php echo esc_html( $course->course_code ); ?></td>
                            <td><?php echo esc_html( $course->course_title ); ?></td>
                            <td><?php echo esc_html( $course->credit_unit ); ?></td>
                            <td><?php echo esc_html( $grade ); ?></td>
                            <td><?php echo $points; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight: bold; background: #fafafa;">
                        <td colspan="2" style="text-align: right;">Total / GPA:</td>
                        <td><?php echo $total_units; ?></td>
                        <td colspan="2">
                            <?php echo $total_units > 0 ? number_format( $total_points / $total_units, 2 ) : '0.00'; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

        <?php endforeach; ?>
    <?php else : ?>
        <p>No academic records found.</p>
    <?php endif; ?>

    <div style="margin-top: 50px; text-align: center;">
        <p>__________________________</p>
        <p><strong>Registrar's Signature & Stamp</strong></p>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <button onclick="window.print()">Print Transcript</button>
    </div>

</body>
</html>
