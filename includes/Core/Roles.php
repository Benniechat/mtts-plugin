<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Roles {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'add_roles' ) );
    }

    public static function add_roles() {
        // Remove existing roles if needed (optional, careful with this)
        // remove_role( 'subscriber' );
        // remove_role( 'editor' );
        // remove_role( 'contributor' );
        // remove_role( 'author' );

        // Super Admin (WordPress Administrator already exists, we can use that or create a specific one)
        // We will piggyback on 'administrator' for Super Admin but add specific caps if needed.

        // School Admin
        add_role( 'mtts_school_admin', 'School Admin', array(
            'read' => true,
            'manage_options' => false,
            'mtts_manage_students' => true,
            'mtts_manage_staff' => true,
            'mtts_manage_courses' => true,
            'mtts_view_reports' => true,
        ));

        // Registrar
        add_role( 'mtts_registrar', 'Registrar', array(
            'read' => true,
            'mtts_manage_admissions' => true,
            'mtts_manage_exams' => true,
            'mtts_generate_reports' => true,
        ));

        // Lecturer
        add_role( 'mtts_lecturer', 'Lecturer', array(
            'read' => true,
            'mtts_manage_classes' => true,
            'mtts_grade_assignments' => true,
            'mtts_view_students' => true,
            'upload_files' => true,
        ));

        // Accountant
        add_role( 'mtts_accountant', 'Accountant', array(
            'read' => true,
            'mtts_manage_payments' => true,
            'mtts_view_financial_reports' => true,
        ));

        // Student
        add_role( 'mtts_student', 'Student', array(
            'read' => true,
            'mtts_view_courses' => true,
            'mtts_view_grades' => true,
            'mtts_register_courses' => true,
            'mtts_pay_fees' => true,
        ));

        // Alumni
        add_role( 'mtts_alumni', 'Alumni', array(
            'read' => true,
            'mtts_access_alumni_network' => true,
        ));

        // Campus Coordinator
        add_role( 'mtts_campus_coordinator', 'Campus Coordinator', array(
            'read' => true,
            'mtts_manage_students' => true,
            'mtts_manage_staff' => true,
            'mtts_view_reports' => true,
        ));
    }

    public static function remove_roles() {
        remove_role( 'mtts_school_admin' );
        remove_role( 'mtts_registrar' );
        remove_role( 'mtts_lecturer' );
        remove_role( 'mtts_accountant' );
        remove_role( 'mtts_student' );
        remove_role( 'mtts_alumni' );
        remove_role( 'mtts_campus_coordinator' );
    }
}
