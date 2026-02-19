<!DOCTYPE html>
<html>
<head>
    <title>Graduation Certificate - MTTS</title>
    <style>
        @page { size: landscape; margin: 0; }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            margin: 0; 
            padding: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background: #fff;
        }
        .border-pattern {
            position: absolute;
            top: 20px; left: 20px; right: 20px; bottom: 20px;
            border: 5px double #4b0082;
            padding: 5px;
        }
        .inner-border {
            border: 2px solid #ffd700;
            height: 100%;
            padding: 40px;
            text-align: center;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.9);
        }
        .header { margin-bottom: 30px; }
        .school-name { font-size: 36px; font-weight: bold; color: #4b0082; text-transform: uppercase; margin: 0; }
        .school-motto { font-size: 14px; letter-spacing: 2px; color: #555; margin-top: 5px; text-transform: uppercase; }
        .cert-title { font-size: 48px; margin: 40px 0 20px; font-weight: normal; color: #333; font-style: italic; font-family: 'Zapfino', cursive; }
        .content { font-size: 18px; line-height: 1.6; margin-bottom: 40px; }
        .student-name { font-size: 32px; font-weight: bold; border-bottom: 1px solid #000; display: inline-block; min-width: 300px; margin: 10px 0; color: #000; }
        .degree-name { font-size: 24px; font-weight: bold; color: #4b0082; display: block; margin: 10px 0; }
        .footer { display: flex; justify-content: space-around; margin-top: 60px; }
        .sig-line { border-top: 1px solid #000; width: 250px; padding-top: 10px; font-size: 14px; font-weight: bold; }
        .seal { width: 120px; height: 120px; background: #ffd700; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #4b0082; font-weight: bold; border: 3px double #4b0082; margin: 0 auto; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="border-pattern">
        <div class="inner-border">
            <div class="header">
                <!-- <img src="logo.png" style="width: 80px; margin-bottom: 10px;"> -->
                <h1 class="school-name">Mountain-Top Theological Seminary</h1>
                <div class="school-motto">Raising Champions for the Kingdom</div>
            </div>

            <h2 class="cert-title">Certificate of Graduation</h2>

            <div class="content">
                <p>This is to certify that</p>
                <div class="student-name"><?php echo esc_html( $user->display_name ); ?></div>
                <p>having fulfilled all the requirements of the Seminary has been awarded the</p>
                <div class="degree-name"><?php echo esc_html( $program->name ); ?></div>
                <p>with all rights, honors, and privileges appertaining thereto.</p>
                <p>Given this <?php echo date('jS'); ?> day of <?php echo date('F, Y'); ?>.</p>
            </div>

            <div class="footer">
                <div style="text-align: center;">
                    <div class="sig-line">Registrar</div>
                </div>
                <div class="seal">
                    OFFICIAL<br>SEAL
                </div>
                <div style="text-align: center;">
                    <div class="sig-line">Rector</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px;">
        <button onclick="window.print()" style="padding: 15px 30px; background: #4b0082; color: #fff; border: none; cursor: pointer; font-size: 16px; border-radius: 5px;">Print Certificate</button>
    </div>
</body>
</html>
