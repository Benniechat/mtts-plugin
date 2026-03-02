<?php
namespace MttsLms\Core\Forms;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FormTemplates {

    public static function get_templates() {
        return array(
            'admission-form' => array(
                'title' => 'Mountain Top Theological Seminary – Online Application Form',
                'type' => 'multistep',
                'save_continue' => 1,
                'requires_payment' => 1,
                'fields' => array(
                    // STEP 1: Personal Information
                    array( 'type' => 'section', 'label' => 'STEP 1: Personal Information', 'required' => false ),
                    array( 'type' => 'select', 'label' => 'Select Campus', 'options' => 'Lagos Campus, Ogun Campus, Abuja Campus, Port Harcourt Campus, Online/Distance Learning', 'required' => true, 'id' => 'selected_campus', 'width' => 'full', 'icon' => 'dashicons-location' ),
                    array( 'type' => 'select', 'label' => 'Title', 'options' => 'Dr., Mr., Mrs., Miss, Bro., Sis.', 'required' => true, 'width' => '1/3', 'icon' => 'dashicons-id' ),
                    array( 'type' => 'text', 'label' => 'Surname', 'placeholder' => '', 'required' => true, 'width' => '1/3', 'icon' => 'dashicons-admin-users' ),
                    array( 'type' => 'text', 'label' => 'Forenames', 'placeholder' => '', 'required' => true, 'width' => '1/3' ),
                    array( 'type' => 'date', 'label' => 'Date of Birth', 'placeholder' => '', 'required' => true, 'id' => 'dob', 'width' => '1/2', 'icon' => 'dashicons-calendar-alt' ),
                    array( 'type' => 'number', 'label' => 'Age', 'placeholder' => 'Auto-calculated', 'required' => false, 'readonly' => true, 'id' => 'age_field', 'width' => '1/2' ),
                    array( 'type' => 'select', 'label' => 'Sex', 'options' => 'Male, Female', 'required' => true, 'width' => '1/3' ),
                    array( 'type' => 'text', 'label' => 'Religion', 'placeholder' => '', 'required' => true, 'width' => '1/3' ),
                    array( 'type' => 'text', 'label' => 'Nationality', 'placeholder' => '', 'required' => true, 'width' => '1/3' ),
                    array( 'type' => 'text', 'label' => 'State of Origin', 'placeholder' => '', 'required' => true, 'width' => '1/2' ),
                    array( 'type' => 'text', 'label' => 'Local Government Area', 'placeholder' => '', 'required' => false, 'width' => '1/2' ),
                    array( 'type' => 'textarea', 'label' => 'Present Home Address', 'placeholder' => '', 'required' => true, 'width' => 'full', 'icon' => 'dashicons-admin-home' ),
                    array( 'type' => 'textarea', 'label' => 'Home Town Address', 'placeholder' => '', 'required' => false, 'width' => 'full' ),
                    array( 'type' => 'tel', 'label' => 'Phone/GSM Number', 'placeholder' => '', 'required' => true, 'width' => '1/2', 'icon' => 'dashicons-phone' ),
                    array( 'type' => 'email', 'label' => 'Email Address', 'placeholder' => '', 'required' => true, 'width' => '1/2', 'icon' => 'dashicons-email' ),
                    array( 'type' => 'select', 'label' => 'Marital Status', 'options' => 'Single, Married, Widowed, Divorced', 'required' => false, 'id' => 'marital_status', 'width' => '1/3' ),
                    array( 'type' => 'text', 'label' => 'Full Name of Spouse', 'placeholder' => '', 'required' => false, 'condition' => 'marital_status==Married', 'width' => '1/3' ),
                    array( 'type' => 'tel', 'label' => 'Spouse Phone Number', 'placeholder' => '', 'required' => false, 'condition' => 'marital_status==Married', 'width' => '1/3' ),
                    array( 'type' => 'text', 'label' => 'Profession/Occupation', 'placeholder' => '', 'required' => false, 'width' => '1/2' ),
                    array( 'type' => 'file', 'label' => 'Passport Photograph', 'placeholder' => '', 'required' => true, 'width' => '1/2', 'icon' => 'dashicons-camera', 'help_text' => 'Upload a clear passport-sized photograph (JPEG/PNG)' ),

                    // STEP 2: Church & Spiritual Background
                    array( 'type' => 'section', 'label' => 'STEP 2: Church & Spiritual Background', 'required' => false ),
                    array( 'type' => 'text', 'label' => 'Church Name', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'Church Address', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'date', 'label' => 'Date Joined Church', 'placeholder' => '', 'required' => false ),
                    array( 'type' => 'textarea', 'label' => 'Group Participation in Church', 'placeholder' => '', 'required' => false ),
                    array( 'type' => 'textarea', 'label' => 'When and Where Did You Become Born Again?', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'Briefly State Your Salvation Experience', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'radio', 'label' => 'Have You Received the Baptism of the Holy Ghost?', 'options' => 'Yes, No', 'required' => true ),

                    // STEP 3: Program & Motivation
                    array( 'type' => 'section', 'label' => 'STEP 3: Program & Motivation', 'required' => false ),
                    array( 'type' => 'radio', 'label' => 'Program of Choice', 'options' => 'Certificate, Diploma, Bachelor', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'State Your Reasons for Applying', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'State Your Spiritual Gifts and Spiritual Inspiration', 'placeholder' => '', 'required' => true ),

                    // STEP 4: Academic Qualifications
                    array( 'type' => 'section', 'label' => 'STEP 4: Academic Qualifications', 'required' => false ),
                    array( 'type' => 'repeater', 'label' => 'Academic History', 'required' => true, 'fields' => 'School Attended(text), Qualification Obtained(text), Date(text), Upload Credential(file)' ),

                    // STEP 5: Professional Qualifications / Employment Background
                    array( 'type' => 'section', 'label' => 'STEP 5: Professional Qualifications', 'required' => false ),
                    array( 'type' => 'repeater', 'label' => 'Employment History', 'required' => false, 'fields' => 'Employment Organization(text), Position Held(text), Date From(date), Date To(date)' ),

                    // STEP 6: Referees
                    array( 'type' => 'section', 'label' => 'STEP 6: Referees', 'required' => false ),
                    array( 'type' => 'html', 'label' => '', 'required' => false, 'content' => '<strong>Referee 1</strong>' ),
                    array( 'type' => 'text', 'label' => 'Referee 1 Name', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'Referee 1 Address', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'tel', 'label' => 'Referee 1 Phone', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'email', 'label' => 'Referee 1 Email', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'text', 'label' => 'Referee 1 Status/Position', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'Referee 1 Recommendation', 'placeholder' => '', 'required' => true ),
                    
                    array( 'type' => 'html', 'label' => '', 'required' => false, 'content' => '<br><strong>Referee 2</strong>' ),
                    array( 'type' => 'text', 'label' => 'Referee 2 Name', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'Referee 2 Address', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'tel', 'label' => 'Referee 2 Phone', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'email', 'label' => 'Referee 2 Email', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'text', 'label' => 'Referee 2 Status/Position', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'Referee 2 Recommendation', 'placeholder' => '', 'required' => true ),

                    // STEP 7: Sponsorship & Funding
                    array( 'type' => 'section', 'label' => 'STEP 7: Sponsorship & Funding', 'required' => false ),
                    array( 'type' => 'textarea', 'label' => 'How do you intend to fund the program?', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'radio', 'label' => 'Is your funding through sponsorship?', 'options' => 'Yes, No', 'required' => true, 'id' => 'is_sponsored' ),
                    array( 'type' => 'text', 'label' => 'Sponsor Name', 'placeholder' => '', 'required' => false, 'condition' => 'is_sponsored==Yes' ),
                    array( 'type' => 'file', 'label' => 'Upload Sponsorship Confirmation Letter', 'placeholder' => '', 'required' => false, 'condition' => 'is_sponsored==Yes' ),

                    // STEP 8: Declaration & Submission
                    array( 'type' => 'section', 'label' => 'STEP 8: Declaration & Submission', 'required' => false ),
                    array( 'type' => 'checkbox', 'label' => 'Declaration', 'options' => 'I hereby declare that all information provided is true and correct and I agree to abide by the rules and regulations of Mountain Top Theological Seminary.', 'required' => true ),
                    array( 'type' => 'text', 'label' => 'Full Name (Declaration)', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'signature', 'label' => 'Digital Signature', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'hidden', 'label' => 'Submission Date', 'required' => false, 'id' => 'current_date' ),
                )
            ),
            'contact' => array(
                'title' => 'Contact Us',
                'fields' => array(
                    array( 'type' => 'text', 'label' => 'Name', 'placeholder' => 'Full Name', 'required' => true ),
                    array( 'type' => 'email', 'label' => 'Email', 'placeholder' => 'Email Address', 'required' => true ),
                    array( 'type' => 'text', 'label' => 'Subject', 'placeholder' => 'Message Subject', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'Message', 'placeholder' => 'Your message here...', 'required' => true ),
                )
            ),
            'feedback' => array(
                'title' => 'Course Feedback',
                'fields' => array(
                    array( 'type' => 'text', 'label' => 'Course Name', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'rating', 'label' => 'Rate the Instructor', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'rating', 'label' => 'Rate the Content', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'textarea', 'label' => 'What can be improved?', 'placeholder' => '', 'required' => false ),
                )
            ),
            'event' => array(
                'title' => 'Event Registration',
                'fields' => array(
                    array( 'type' => 'text', 'label' => 'Attendee Name', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'email', 'label' => 'Email', 'placeholder' => '', 'required' => true ),
                    array( 'type' => 'radio', 'label' => 'Registration Type', 'options' => 'Student, Faculty, Guest', 'required' => true ),
                    array( 'type' => 'checkbox', 'label' => 'Workshops', 'options' => 'Theological Foundations, Advanced Leadership', 'required' => false ),
                )
            ),
        );
    }
}
