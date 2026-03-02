<!DOCTYPE html>
<html>
<head>
    <title>Admission Letter - MTTS</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 40px; max-width: 800px; margin: 0 auto; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { max-width: 100px; margin-bottom: 15px; }
        .title { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 16px; margin-top: 5px; }
        .content { font-size: 16px; text-align: justify; }
        .details-table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .details-table td { padding: 8px; border: 1px solid #ddd; }
        .details-table strong { font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; font-size: 14px; }
        .signature { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig-block { text-align: center; border-top: 1px solid #000; width: 200px; padding-top: 10px; }
        .print-btn { text-align: center; margin-bottom: 20px; }
        @media print { .print-btn { display: none; } body { padding: 0; } }
    </style>
</head>
<body>

    <div class="print-btn">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print Admission Letter</button>
    </div>

    <div class="header">
        <img src="<?php echo MTTS_LMS_URL . 'assets/images/logo-mtts.jpg'; ?>" class="logo" style="border-radius: 10px;">
        <div class="title"><?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?></div>
        <div class="subtitle">Raising Champions for the Kingdom</div>
        <p>OFFICE OF THE REGISTRAR</p>
    </div>

    <div class="content">
        <p style="text-align: right;"><strong>Date:</strong> <?php echo date('F j, Y'); ?></p>
        
        <p><strong>Dear <?php echo esc_html( $user->display_name ); ?>,</strong></p>

        <h3 style="text-align: center; text-decoration: underline;">OFFER OF PROVISIONAL ADMISSION</h3>

        <p>I am pleased to inform you that you have been offered provisional admission into the <?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?> to pursue a course of study leading to the award of:</p>

        <h3 style="text-align: center;"><?php echo esc_html( $program->name ); ?></h3>

        <p>Your admission details are as follows:</p>

        <table class="details-table">
            <tr>
                <td><strong>Matriculation Number:</strong></td>
                <td><?php echo esc_html( $student->matric_number ); ?></td>
            </tr>
            <tr>
                <td><strong>Academic Session:</strong></td>
                <td><?php echo esc_html( $session->name ); ?></td>
            </tr>
            <tr>
                <td><strong>Department:</strong></td>
                <td>Theology</td>
            </tr>
            <tr>
                <td><strong>Duration:</strong></td>
                <td><?php echo esc_html( $program->duration_years ); ?> Years</td>
            </tr>
        </table>

        <p>This offer is subject to your acceptance of the rules and regulations of the Seminary and the payment of all necessary fees. Please note that this offer may be withdrawn if it is discovered that you do not possess the qualifications which you claimed to have obtained.</p>

        <p>Congratulations on your admission.</p>
    </div>

    <div class="signature">
        <div class="sig-block">
            <strong>Registrar</strong>
        </div>
        <div class="sig-block">
            <strong>Rector</strong>
        </div>
    </div>

    <div class="footer">
        <p><?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?> | www.mtts.edu.ng | info@mtts.edu.ng</p>
    </div>

</body>
</html>
