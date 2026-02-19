<?php
/**
 * Lecturer Resource Library View
 * Allows lecturers to upload and manage course resources
 */
$current_user_id = get_current_user_id();

// Handle upload
if ( isset( $_POST['mtts_resource_action'] ) && $_POST['mtts_resource_action'] === 'upload' && check_admin_referer( 'mtts_resource_upload' ) ) {
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $url = '';
    if ( ! empty( $_FILES['resource_file']['name'] ) ) {
        $upload = wp_handle_upload( $_FILES['resource_file'], [ 'test_form' => false ] );
        if ( $upload && ! isset( $upload['error'] ) ) {
            $url = $upload['url'];
        }
    } elseif ( ! empty( $_POST['resource_url'] ) ) {
        $url = esc_url_raw( $_POST['resource_url'] );
    }

    if ( $url ) {
        \MttsLms\Models\Resource::create( [
            'title'       => sanitize_text_field( $_POST['title'] ),
            'description' => sanitize_textarea_field( $_POST['description'] ?? '' ),
            'type'        => sanitize_key( $_POST['type'] ),
            'url'         => $url,
            'course_id'   => intval( $_POST['course_id'] ) ?: null,
            'uploaded_by' => $current_user_id,
        ] );
        echo '<div class="mtts-alert mtts-alert-success">Resource uploaded successfully!</div>';
    }
}

// Handle delete
if ( isset( $_GET['delete_resource'] ) && check_admin_referer( 'mtts_delete_resource_' . intval( $_GET['delete_resource'] ) ) ) {
    \MttsLms\Models\Resource::delete( intval( $_GET['delete_resource'] ) );
    wp_redirect( remove_query_arg( [ 'delete_resource', '_wpnonce' ] ) );
    exit;
}

$my_resources = \MttsLms\Models\Resource::get_by_lecturer( $current_user_id );

// Get courses this lecturer teaches
global $wpdb;
$courses_table = $wpdb->prefix . 'mtts_courses';
$my_courses    = $wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM {$courses_table} WHERE lecturer_id = %d ORDER BY course_code ASC",
    $current_user_id
) );
?>
<div class="mtts-dashboard-section">
    <h2>📚 Resource Library</h2>

    <div style="display:grid; grid-template-columns: 1fr 1.5fr; gap:30px; margin-top:20px;">

        <!-- Upload Form -->
        <div>
            <h3>Upload New Resource</h3>
            <div class="mtts-card">
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="mtts_resource_action" value="upload">
                    <?php wp_nonce_field( 'mtts_resource_upload' ); ?>
                    <div class="mtts-form-group">
                        <label>Title <span style="color:red;">*</span></label>
                        <input type="text" name="title" class="mtts-form-control" required placeholder="Resource title">
                    </div>
                    <div class="mtts-form-group">
                        <label>Description</label>
                        <textarea name="description" class="mtts-form-control" rows="2" placeholder="Brief description (optional)"></textarea>
                    </div>
                    <div class="mtts-form-group">
                        <label>Type</label>
                        <select name="type" class="mtts-form-control">
                            <option value="pdf">📄 PDF</option>
                            <option value="video">🎬 Video</option>
                            <option value="ebook">📖 eBook</option>
                            <option value="audio">🎵 Audio</option>
                            <option value="link">🔗 External Link</option>
                            <option value="other">📁 Other</option>
                        </select>
                    </div>
                    <div class="mtts-form-group">
                        <label>Linked Course (optional)</label>
                        <select name="course_id" class="mtts-form-control">
                            <option value="">-- General (All Students) --</option>
                            <?php foreach ( $my_courses as $c ) : ?>
                                <option value="<?php echo $c->id; ?>"><?php echo esc_html( $c->course_code . ' — ' . $c->course_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mtts-form-group">
                        <label>Upload File</label>
                        <input type="file" name="resource_file" class="mtts-form-control">
                    </div>
                    <div class="mtts-form-group">
                        <label>— OR — External URL</label>
                        <input type="url" name="resource_url" class="mtts-form-control" placeholder="https://...">
                    </div>
                    <button type="submit" class="mtts-btn mtts-btn-primary">Upload Resource</button>
                </form>
            </div>
        </div>

        <!-- My Resources -->
        <div>
            <h3>My Uploaded Resources (<?php echo count( $my_resources ); ?>)</h3>
            <div class="mtts-card">
                <?php if ( empty( $my_resources ) ) : ?>
                    <p style="color:#999; text-align:center; padding:20px;">No resources uploaded yet.</p>
                <?php else : ?>
                    <table class="mtts-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Course</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $my_resources as $res ) :
                                $icon   = \MttsLms\Models\Resource::get_icon( $res->type );
                                $course = $res->course_id ? \MttsLms\Models\Course::find( $res->course_id ) : null;
                            ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo esc_url( $res->url ); ?>" target="_blank"><?php echo $icon; ?> <?php echo esc_html( $res->title ); ?></a>
                                    </td>
                                    <td><?php echo esc_html( strtoupper( $res->type ) ); ?></td>
                                    <td><?php echo $course ? esc_html( $course->course_code ) : '<em>General</em>'; ?></td>
                                    <td style="font-size:0.85rem; color:#999;"><?php echo date( 'M j, Y', strtotime( $res->created_at ) ); ?></td>
                                    <td>
                                        <a href="<?php echo wp_nonce_url( add_query_arg( 'delete_resource', $res->id ), 'mtts_delete_resource_' . $res->id ); ?>"
                                           onclick="return confirm('Delete this resource?')"
                                           style="color:#e53e3e; font-size:0.85rem;">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
