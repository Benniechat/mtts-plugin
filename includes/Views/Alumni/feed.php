<div class="mtts-alumni-nexus">
    <!-- Left Sidebar: Mini Profile -->
    <div class="nexus-sidebar-left">
        <?php 
        $u = wp_get_current_user();
        $my_profile = \MttsLms\Models\AlumniProfile::get_by_user( $u->ID );
        ?>
        <div class="koinonia-glass mtts-social-card" style="padding:0; overflow:hidden;">
            <div style="height:70px; background:<?php echo ($my_profile->banner_url ?? '') ? 'url('.$my_profile->banner_url.')' : 'linear-gradient(135deg, #7c3aed, #fbbf24)'; ?>; background-size:cover;"></div>
            <div style="padding:0 20px 25px; text-align:center;">
                <img src="<?php echo $my_profile->profile_picture_url ?? get_avatar_url( $u->ID ); ?>" style="width:70px; height:70px; border-radius:50%; border:3px solid #fff; margin-top:-35px; box-shadow:0 4px 10px rgba(0,0,0,0.1); object-fit:cover;">
                <h4 style="margin:15px 0 5px;"><?php echo esc_html($u->display_name); ?></h4>
                <p style="color:#64748b; font-size:12px; margin-bottom:15px;"><?php echo esc_html($my_profile->headline ?? 'Minister of the Gospel'); ?></p>
                <div style="border-top:1px solid #f1f5f9; padding-top:15px; text-align:left;">
                    <a href="?view=profile" style="text-decoration:none; color:#7c3aed; font-size:12px; font-weight:700;">View Full Profile</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Feed -->
    <div class="nexus-main-feed">
        <!-- Post Box (Premium LinkedIn/Facebook style) -->
        <div class="mtts-social-card koinonia-glass" style="padding:20px; border: 1px solid rgba(0,0,0,0.08);">
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field( 'mtts_alumni_social' ); ?>
                <input type="hidden" name="mtts_alumni_action" value="create_post">
                <div style="display:flex; gap:12px; align-items: center;">
                    <img src="<?php echo $my_profile->profile_picture_url ?? get_avatar_url( $u->ID ); ?>" style="width:48px; height:48px; border-radius:50%; object-fit:cover; border: 1px solid #eee;">
                    <button type="button" class="mtts-post-input" style="flex:1; text-align:left; color:#666; border:1px solid #ddd; background:#f0f2f5; padding:12px 20px; border-radius:35px; cursor:pointer; font-weight:500;" onclick="document.querySelector('#mtts-post-textarea').focus();">
                        Start a spiritual conversation or share a testimony...
                    </button>
                </div>
                
                <div id="post-expanded-area" style="margin-top:15px; display:none;">
                    <textarea id="mtts-post-textarea" name="content" rows="4" placeholder="What's on your heart, Minister?" style="width:100%; border:none; background:transparent; padding:10px; font-size:16px; resize:none; outline:none;"></textarea>
                    
                    <div id="media-preview" style="margin-top:15px; display:none;">
                        <div style="position:relative; display:inline-block; width:100%;">
                            <img id="preview-img" style="width:100%; border-radius:12px; border: 1px solid #ddd;">
                            <video id="preview-vid" style="width:100%; border-radius:12px; display:none;" controls></video>
                            <button type="button" onclick="clearMedia()" style="position:absolute; top:10px; right:10px; background:rgba(0,0,0,0.6); color:white; border:none; border-radius:50%; width:30px; height:30px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px;">&times;</button>
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; padding-top:10px; border-top:1px solid rgba(0,0,0,0.05);">
                    <div style="display:flex; gap:5px;">
                        <label class="amen-btn" style="cursor:pointer; color:#0a66c2;">
                             <span class="dashicons dashicons-format-image" style="color: #378fe9;"></span> Photo
                             <input type="file" name="media_file" class="mtts-file-input" accept="image/*" onchange="previewMedia(this); expandPost();" style="display:none;">
                        </label>
                        <label class="amen-btn" style="cursor:pointer; color:#5f9b41;">
                             <span class="dashicons dashicons-video-alt3" style="color: #5f9b41;"></span> Video
                             <input type="file" name="media_file" class="mtts-file-input" accept="video/*" onchange="previewMedia(this); expandPost();" style="display:none;">
                        </label>
                    </div>
                    <button type="submit" class="mtts-btn mtts-btn-primary" style="border-radius:20px; padding: 8px 20px; font-weight:600;">Propagate</button>
                </div>
            </form>
        </div>

        <!-- Feed Items -->
        <?php foreach ( $posts as $post ) : ?>
            <div class="koinonia-glass mtts-social-card" style="padding:20px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                    <div style="display:flex; gap:12px;">
                        <img src="<?php echo get_avatar_url( $post->author_id ); ?>" style="width:48px; height:48px; border-radius:12px; object-fit:cover;">
                        <div>
                            <h5 style="margin:0; font-size:15px;"><?php echo esc_html($post->display_name); ?></h5>
                            <p style="margin:0; font-size:12px; color:#64748b;"><?php echo human_time_diff(strtotime($post->created_at), current_time('timestamp')); ?> ago</p>
                        </div>
                    </div>
                </div>

                <div style="font-size:15px; line-height:1.6; color:#1e293b; margin-bottom:15px;">
                    <?php echo nl2br(esc_html($post->content)); ?>
                </div>

                <?php if ( $post->media_url ) : ?>
                    <div class="mtts-social-media-container" style="margin-bottom:15px;">
                        <?php if ( $post->media_type === 'video' ) : ?>
                            <video class="mtts-feed-media" controls><source src="<?php echo esc_url($post->media_url); ?>"></video>
                        <?php else : ?>
                            <img src="<?php echo esc_url($post->media_url); ?>" class="mtts-feed-media">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div style="display:flex; gap:20px; border-top:1px solid #f1f5f9; padding-top:15px;">
                    <button type="button" class="amen-btn <?php echo $post->likes_count > 0 ? 'active' : ''; ?>" onclick="socialAction('amen_post', <?php echo $post->id; ?>, this)">
                        <span class="dashicons dashicons-heart"></span> Amen <?php echo $post->likes_count ?: ''; ?>
                    </button>
                    <button type="button" class="amen-btn" onclick="toggleComments(<?php echo $post->id; ?>)">
                        <span class="dashicons dashicons-admin-comments"></span> Feedback (<?php echo $post->comments_count; ?>)
                    </button>
                </div>
                
                <!-- Comments Section (Hidden by default) -->
                <div id="comments-<?php echo $post->id; ?>" style="display:none; margin-top:20px; border-top:1px solid #f1f5f9; padding-top:20px;">
                    <!-- Loading via JS or existing logic -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Right Sidebar: Trends & Fellowship -->
    <div class="nexus-sidebar-right">
        <div class="koinonia-glass mtts-social-card">
            <h4>Fellowship Suggestions</h4>
            <p style="font-size:12px; color:#64748b;">Ministers you might know</p>
            <!-- Loop through alumni suggestions -->
            <a href="?view=directory" style="display:block; text-align:center; padding-top:10px; font-size:12px; color:#7c3aed; font-weight:700; text-decoration:none;">View All</a>
        </div>
    </div>
</div>

<script>
function expandPost() {
    document.getElementById('post-expanded-area').style.display = 'block';
    document.querySelector('.mtts-post-input').style.display = 'none';
}

document.querySelector('.mtts-post-input')?.addEventListener('click', expandPost);

function previewMedia(input) {
    const preview = document.getElementById('media-preview');
    const img = document.getElementById('preview-img');
    const vid = document.getElementById('preview-vid');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.style.display = 'block';
            if (file.type.startsWith('video')) {
                img.style.display = 'none';
                vid.style.display = 'block';
                vid.src = e.target.result;
            } else {
                vid.style.display = 'none';
                img.style.display = 'block';
                img.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
}

function clearMedia() {
    document.querySelector('.mtts-file-input').value = '';
    document.getElementById('media-preview').style.display = 'none';
}

function socialAction(action, postId, btn) {
    const formData = new FormData();
    formData.append('mtts_alumni_action', action);
    formData.append('post_id', postId);
    formData.append('_wpnonce', '<?php echo wp_create_nonce("mtts_alumni_social"); ?>');

    fetch('', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => { if(data.success) location.reload(); });
}

function toggleComments(id) {
    const el = document.getElementById('comments-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
