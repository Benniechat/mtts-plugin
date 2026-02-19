<?php
namespace MttsLms\Core;

use MttsLms\Models\Submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PlagiarismChecker {

    /**
     * Check submission against others in the same assignment
     * @param int $submission_id
     * @return float Max similarity percentage
     */
    public static function check( $submission_id ) {
        $submission = Submission::find( $submission_id );
        if ( ! $submission ) return 0;

        $others = Submission::get_by_assignment( $submission->assignment_id );
        
        $max_similarity = 0;
        $current_text = strip_tags( $submission->content );
        
        if ( empty( $current_text ) || strlen( $current_text ) < 50 ) {
            return 0; // Too short to check
        }

        foreach ( $others as $other ) {
            if ( $other->id == $submission_id ) continue;

            $other_text = strip_tags( $other->content );
            if ( empty( $other_text ) ) continue;

            $similarity = 0;
            similar_text( $current_text, $other_text, $similarity );

            if ( $similarity > $max_similarity ) {
                $max_similarity = $similarity;
            }
        }

        // Update score
        Submission::update( $submission_id, array( 'plagiarism_score' => $max_similarity ) );

        return $max_similarity;
    }
}
