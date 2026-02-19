<?php
/**
 * Admin Student Promotion Interface
 * Allows admin to bulk-promote students from one level to the next.
 */

// Handle promotion action
if ( isset( $_POST['mtts_promote_students'] ) && check_admin_referer( 'mtts_promote_students' ) ) {
    $student_ids  = array_map( 'intval', $_POST['student_ids'] ?? [] );
    $target_level = sanitize_text_field( $_POST['target_level'] );
    $promoted     = 0;

    foreach ( $student_ids as $sid ) {
        global $wpdb;
        $students_table = $wpdb->prefix . 'mtts_students';
        $updated = $wpdb->update( $students_table, [ 'current_level' => $target_level ], [ 'id' => $sid ] );
        if ( $updated ) {
            $promoted++;
            // Notify student
            $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$students_table} WHERE id = %d", $sid ) );
            if ( $student ) {
                $user = get_userdata( $student->user_id );
                if ( $user ) {
                    wp_mail(
                        $user->user_email,
                        'Congratulations! You Have Been Promoted',
                        "Dear {$user->display_name},\n\nYou have been promoted to {$target_level}.\n\nCongratulations from Mountain-Top Theological Seminary!\n\nGod bless you."
                    );
                }
            }
        }
    }

    echo '<div class="notice notice-success is-dismissible"><p>' . $promoted . ' student(s) promoted to ' . esc_html( $target_level ) . ' successfully.</p></div>';
}

// Fetch students grouped by level
global $wpdb;
$students_table = $wpdb->prefix . 'mtts_students';
$all_students   = $wpdb->get_results(
    "SELECT s.*, u.display_name, u.user_email FROM {$students_table} s
     LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
     WHERE s.status = 'active'
     ORDER BY s.current_level ASC, u.display_name ASC"
);

$levels = [ 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Graduated' ];
$students_by_level = [];
foreach ( $all_students as $s ) {
    $students_by_level[ $s->current_level ][] = $s;
}
?>
<div class="wrap">
    <h1>🎓 Student Promotion</h1>
    <p>Select students to promote and choose their new level. An email notification will be sent to each promoted student.</p>

    <form method="post" action="">
        <?php wp_nonce_field( 'mtts_promote_students' ); ?>

        <div style="display:flex; gap:20px; margin-bottom:20px; align-items:flex-end;">
            <div>
                <label><strong>Promote Selected Students To:</strong></label><br>
                <select name="target_level" class="regular-text" required>
                    <?php foreach ( $levels as $level ) : ?>
                        <option value="<?php echo esc_attr( $level ); ?>"><?php echo esc_html( $level ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <?php submit_button( '🎓 Promote Selected', 'primary', 'mtts_promote_students', false ); ?>
            </div>
        </div>

        <?php foreach ( $levels as $level ) :
            $level_students = $students_by_level[ $level ] ?? [];
            if ( empty( $level_students ) ) continue;
        ?>
            <h3><?php echo esc_html( $level ); ?> (<?php echo count( $level_students ); ?> students)</h3>
            <table class="wp-list-table widefat fixed striped" style="margin-bottom:25px;">
                <thead>
                    <tr>
                        <th style="width:30px;"><input type="checkbox" onclick="toggleLevel(this, '<?php echo esc_js( $level ); ?>')"></th>
                        <th>Name</th>
                        <th>Matric Number</th>
                        <th>Email</th>
                        <th>Program</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $level_students as $s ) :
                        $program = \MttsLms\Models\Program::find( $s->program_id );
                    ?>
                        <tr>
                            <td><input type="checkbox" name="student_ids[]" value="<?php echo $s->id; ?>" class="level-<?php echo sanitize_html_class( $level ); ?>"></td>
                            <td><strong><?php echo esc_html( $s->display_name ); ?></strong></td>
                            <td><code><?php echo esc_html( $s->matric_number ); ?></code></td>
                            <td><?php echo esc_html( $s->user_email ); ?></td>
                            <td><?php echo esc_html( $program ? $program->name : '—' ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <?php if ( empty( $all_students ) ) : ?>
            <p>No active students found.</p>
        <?php endif; ?>
    </form>
</div>

<script>
function toggleLevel(masterCheckbox, level) {
    var checkboxes = document.querySelectorAll('.level-' + level.replace(/\s+/g, '-'));
    checkboxes.forEach(function(cb) { cb.checked = masterCheckbox.checked; });
}
</script>
