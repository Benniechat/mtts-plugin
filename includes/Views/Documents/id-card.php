<!DOCTYPE html>
<html>
<head>
    <title>Student ID Card - MTTS</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f0f0; margin: 0; }
        .id-card { width: 350px; height: 220px; background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); overflow: hidden; position: relative; border: 1px solid #ccc; }
        .header { background: #4b0082; color: #fff; padding: 10px; text-align: center; } // Indigo/Purple
        .header h3 { margin: 0; font-size: 14px; text-transform: uppercase; }
        .header p { margin: 0; font-size: 10px; }
        .content { display: flex; padding: 15px; align-items: center; }
        .photo { width: 80px; height: 100px; background: #eee; border: 1px solid #ddd; object-fit: cover; margin-right: 15px; }
        .details { font-size: 12px; line-height: 1.4; color: #333; }
        .details strong { display: block; color: #555; font-size: 10px; }
        .footer { background: #4b0082; height: 20px; position: absolute; bottom: 0; width: 100%; }
        .matric { font-weight: bold; font-size: 14px; margin-top: 5px; color: #4b0082; }
    </style>
</head>
<body>
    <div class="id-card">
        <div class="header">
            <h3><?php echo esc_html(get_option('mtts_institution_name', 'Mountain-Top Theological Seminary')); ?></h3>
            <p>Student Identity Card</p>
        </div>
        <div class="content">
            <img src="<?php echo get_avatar_url( $user->ID, array('size' => 100) ); ?>" class="photo" alt="Photo">
            <div class="details">
                <div>
                    <strong>Name</strong>
                    <?php echo esc_html( $user->display_name ); ?>
                </div>
                <div style="margin-top: 5px;">
                    <strong>Program</strong>
                    <?php echo esc_html( $program->code ); ?>
                </div>
                <div class="matric"><?php echo esc_html( $student->matric_number ); ?></div>
                <div style="margin-top: 5px; font-size: 10px; color: #777;">Exp: Dec 2026</div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
    
    <div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); text-align: center;">
        <button onclick="window.print()">Print ID Card</button>
    </div>
</body>
</html>
