<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Translator {
    
    public static function init() {
        if ( get_option( 'mtts_enable_google_translator' ) ) {
            add_action( 'wp_footer', array( __CLASS__, 'render_google_translate_widget' ) );
        }
    }

    public static function render_google_translate_widget() {
        ?>
        <div id="google_translate_element" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>
        <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                    autoDisplay: false
                }, 'google_translate_element');
            }
        </script>
        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
        <style>
            .goog-te-banner-frame.skiptranslate { display: none !important; } 
            body { top: 30px !important; }
            @media print { #google_translate_element { display: none !important; } }
        </style>
        <?php
    }

    public static function get_current_lang() {
        if ( isset( $_GET['lang'] ) ) {
            setcookie( 'mtts_lang', $_GET['lang'], time() + 3600 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN );
            return $_GET['lang'];
        }
        return isset( $_COOKIE['mtts_lang'] ) ? $_COOKIE['mtts_lang'] : 'en';
    }

    public static function trans( $key ) {
        $lang = self::get_current_lang();
        $translations = self::get_translations();
        
        if ( isset( $translations[$lang][$key] ) ) {
            return $translations[$lang][$key];
        }
        
        // Fallback to English
        return isset( $translations['en'][$key] ) ? $translations['en'][$key] : $key;
    }

    private static function get_translations() {
        return array(
            'en' => array(
                'dashboard' => 'Dashboard',
                'my_profile' => 'My Profile',
                'courses' => 'Courses',
                'payments' => 'Payments',
                'exams' => 'Exams',
                'assignments' => 'Assignments',
                'calendar' => 'Calendar',
                'admission_letter' => 'Admission Letter',
                'id_card' => 'ID Card',
                'transcript' => 'Transcript',
                'logout' => 'Logout',
                'student_portal' => 'Student Portal',
                'lecturer_portal' => 'Lecturer Portal',
                'welcome' => 'Welcome',
            ),
            'fr' => array(
                'dashboard' => 'Tableau de bord',
                'my_profile' => 'Mon profil',
                'courses' => 'Cours',
                'payments' => 'Paiements',
                'exams' => 'Examens',
                'assignments' => 'Devoirs',
                'calendar' => 'Calendrier',
                'admission_letter' => 'Lettre d\'admission',
                'id_card' => 'Carte d\'identité',
                'transcript' => 'Relevé de notes',
                'logout' => 'Déconnexion',
                'student_portal' => 'Portail Étudiant',
                'lecturer_portal' => 'Portail Enseignant',
                'welcome' => 'Bienvenue',
            ),
            'yo' => array(
                'dashboard' => 'Oju-ile',
                'my_profile' => 'Profaili Mi',
                'courses' => 'Awọn Ẹkọ',
                'payments' => 'Awọn Isanwo',
                'exams' => 'Idanwo',
                'assignments' => 'Iṣẹ Amurele',
                'calendar' => 'Kalẹnda',
                'admission_letter' => 'Iwe Gbigba',
                'id_card' => 'Kaadi Idanimọ',
                'transcript' => 'Iwe Eri',
                'logout' => 'Jade',
                'student_portal' => 'Oju-ọna Akẹkọ',
                'lecturer_portal' => 'Oju-ọna Olukọ',
                'welcome' => 'Kaabo',
            ),
        );
    }
}
