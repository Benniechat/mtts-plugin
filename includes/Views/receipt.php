<div class="mtts-receipt" style="border: 2px solid #ccc; padding: 40px; max-width: 600px; margin: 0 auto; font-family: sans-serif;">
    <div style="text-align: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">
        <h2><?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?></h2>
        <p>Payment Receipt</p>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 10px;"><strong>Date:</strong></td>
            <td style="padding: 10px; text-align: right;"><?php echo esc_html( date( 'd M Y, h:i A', strtotime( $transaction->paid_at ) ) ); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px;"><strong>Receipt No:</strong></td>
            <td style="padding: 10px; text-align: right;"><?php echo esc_html( $transaction->reference ); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px;"><strong>Student Name:</strong></td>
            <td style="padding: 10px; text-align: right;"><?php echo esc_html( $student_user->display_name ); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px;"><strong>Matric No:</strong></td>
            <td style="padding: 10px; text-align: right;"><?php echo esc_html( $student->matric_number ); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px;"><strong>Session:</strong></td>
            <td style="padding: 10px; text-align: right;"><?php echo esc_html( $session ? $session->name : 'N/A' ); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px;"><strong>Payment Purpose:</strong></td>
            <td style="padding: 10px; text-align: right;"><?php echo esc_html( ucfirst( $transaction->purpose ) ); ?></td>
        </tr>
        <tr style="background: #f9f9f9; font-weight: bold; font-size: 1.2em;">
            <td style="padding: 15px;">Amount Paid:</td>
            <td style="padding: 15px; text-align: right;">₦<?php echo number_format( $transaction->amount, 2 ); ?></td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 40px; color: #777; font-size: 0.9em;">
        <p>Status: <span style="color: green; font-weight: bold;"><?php echo strtoupper( $transaction->status ); ?></span></p>
        <p>Examples 12:1 - "To serve is to reign."</p>
        <button onclick="window.print()" style="margin-top: 20px; padding: 10px 20px; cursor: pointer;">Print Receipt</button>
    </div>
</div>
