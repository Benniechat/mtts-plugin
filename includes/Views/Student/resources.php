<?php
/**
 * Student Resource Library View
 * Shows resources from enrolled courses + general resources
 */
$session = \MttsLms\Models\Session::get_active_session();
$resources = $session
    ? \MttsLms\Models\Resource::get_for_student( $student->id, $session->id )
    : \MttsLms\Models\Resource::get_all();

$type_filter = isset( $_GET['rtype'] ) ? sanitize_key( $_GET['rtype'] ) : '';
if ( $type_filter ) {
    $resources = array_filter( $resources, fn( $r ) => $r->type === $type_filter );
}

$types = [ 'pdf' => '📄 PDF', 'video' => '🎬 Video', 'ebook' => '📖 eBook', 'audio' => '🎵 Audio', 'link' => '🔗 Link' ];
?>
<div class="mtts-dashboard-section">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2>📚 Resource Library</h2>
    </div>

    <!-- Type Filter -->
    <div style="margin-bottom:15px; display:flex; gap:8px; flex-wrap:wrap;">
        <a href="?view=resources" class="mtts-btn <?php echo ! $type_filter ? 'mtts-btn-primary' : ''; ?>">All</a>
        <?php foreach ( $types as $key => $label ) : ?>
            <a href="?view=resources&rtype=<?php echo $key; ?>" class="mtts-btn <?php echo $type_filter === $key ? 'mtts-btn-primary' : ''; ?>"><?php echo $label; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="mtts-card">
        <?php if ( empty( $resources ) ) : ?>
            <p style="text-align:center; color:#999; padding:30px;">No resources available yet. Check back later.</p>
        <?php else : ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:15px; padding:10px;">
                <?php foreach ( $resources as $res ) :
                    $uploader = get_userdata( $res->uploaded_by );
                    $icon     = \MttsLms\Models\Resource::get_icon( $res->type );
                    $course   = $res->course_id ? \MttsLms\Models\Course::find( $res->course_id ) : null;
                ?>
                    <div style="border:1px solid #eee; border-radius:10px; padding:18px; background:#fff; transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                        <div style="font-size:2.5rem; margin-bottom:10px;"><?php echo $icon; ?></div>
                        <h4 style="margin:0 0 6px; font-size:1rem;"><?php echo esc_html( $res->title ); ?></h4>
                        <?php if ( $res->description ) : ?>
                            <p style="color:#666; font-size:0.85rem; margin:0 0 10px;"><?php echo esc_html( $res->description ); ?></p>
                        <?php endif; ?>
                        <div style="font-size:0.8rem; color:#999; margin-bottom:12px;">
                            <?php if ( $course ) : ?><span style="background:#f0f4ff; padding:2px 8px; border-radius:20px;"><?php echo esc_html( $course->course_code ); ?></span><?php endif; ?>
                            &nbsp;By <?php echo esc_html( $uploader ? $uploader->display_name : 'Admin' ); ?>
                        </div>
                        <a href="<?php echo esc_url( $res->url ); ?>" target="_blank" class="mtts-btn mtts-btn-primary" style="width:100%; text-align:center; display:block;">
                            <?php echo $res->type === 'link' ? '🔗 Open Link' : '⬇️ Download'; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
