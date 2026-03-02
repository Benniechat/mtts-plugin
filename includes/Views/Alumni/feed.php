<?php 
$u = wp_get_current_user();
$my_profile = \MttsLms\Models\AlumniProfile::get_by_user( $u->ID );
?>
<div class="mtts-alumni-nexus">
    <!-- 1. Facebook Style Stories (Top) -->
    <div class="fb-stories-container">
        <!-- Create Story -->
        <div class="fb-story-card" style="background: #fff; border: 1px solid var(--lms-border);">
             <div style="height: 130px; overflow:hidden; position:relative;">
                <img src="<?php echo get_avatar_url( $u->ID ); ?>" style="width:100%; height:100%; object-fit:cover;">
             </div>
             <div style="position:absolute; bottom:0; left:0; width:100%; height:60px; background:white; text-align:center;">
                <div style="width:32px; height:32px; background:var(--fb-blue); border-radius:50%; border:3px solid white; display:flex; align-items:center; justify-content:center; color:white; margin:-16px auto 5px; font-weight:bold; font-size:20px;">+</div>
                <span style="font-size:12px; font-weight:600; color:#050505;">Create Story</span>
             </div>
        </div>
        
        <?php 
        $story_alumni = [
            (object)['name' => 'John Doe', 'img' => 'https://i.pravatar.cc/150?u=1'],
            (object)['name' => 'Jane Smith', 'img' => 'https://i.pravatar.cc/150?u=2'],
            (object)['name' => 'Grace Lee', 'img' => 'https://i.pravatar.cc/150?u=3'],
            (object)['name' => 'Samuel Ade', 'img' => 'https://i.pravatar.cc/150?u=4'],
        ];
        foreach($story_alumni as $alum): ?>
            <div class="fb-story-card">
                <img src="<?php echo $alum->img; ?>" class="story-bg">
                <img src="<?php echo $alum->img; ?>" class="fb-story-avatar">
                <span class="fb-story-name"><?php echo $alum->name; ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 2. Post Box (Facebook style) - UNDER STORIES -->
    <div class="mtts-social-card koinonia-glass" style="padding:12px 16px; margin-bottom:16px;">
        <form method="post" action="" enctype="multipart/form-data">
            <?php wp_nonce_field( 'mtts_alumni_social' ); ?>
            <input type="hidden" name="mtts_alumni_action" value="create_post">
            <div style="display:flex; gap:8px; align-items: center; border-bottom: 1px solid #E4E6EB; padding-bottom:12px;">
                <img src="<?php echo $my_profile->profile_picture_url ?? get_avatar_url( $u->ID ); ?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                <button type="button" class="mtts-post-input" style="flex:1; text-align:left; color:#65676B; border:none; background:#F0F2F5; padding:8px 12px; border-radius:20px; cursor:pointer; font-size:15px;" onclick="document.querySelector('#mtts-post-textarea').focus();">
                    What's on your mind, <?php echo esc_html($u->first_name ?: $u->display_name); ?>?
                </button>
            </div>
            
            <div id="post-expanded-area" style="margin-top:15px; display:none;">
                <textarea id="mtts-post-textarea" name="content" rows="4" placeholder="Share your testimony, a prayer point, or ministry update..." style="width:100%; border:none; background:transparent; padding:10px; font-size:16px; resize:none; outline:none;"></textarea>
                
                <div id="media-preview" style="margin-top:15px; display:none;">
                    <div style="position:relative; display:inline-block; width:100%;">
                        <img id="preview-img" style="width:100%; border-radius:12px; border: 1px solid #ddd;">
                        <video id="preview-vid" style="width:100%; border-radius:12px; display:none;" controls></video>
                        <button type="button" onclick="clearMedia()" style="position:absolute; top:10px; right:10px; background:rgba(0,0,0,0.6); color:white; border:none; border-radius:50%; width:30px; height:30px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px;">&times;</button>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:space-around; align-items:center; margin-top:8px;">
                <label class="amen-btn" style="cursor:pointer; display:flex; align-items:center; gap:8px; padding: 8px; flex:1; justify-content:center; border-radius:4px;">
                     <span class="dashicons dashicons-video-alt3" style="color: #F3425F;"></span> 
                     <span style="font-size:14px; color:#65676B; font-weight:600;">Live</span>
                </label>
                <label class="amen-btn" style="cursor:pointer; display:flex; align-items:center; gap:8px; padding: 8px; flex:1; justify-content:center; border-radius:4px;">
                     <span class="dashicons dashicons-format-image" style="color: #45BD62;"></span> 
                     <span style="font-size:14px; color:#65676B; font-weight:600;">Photo</span>
                     <input type="file" name="media_file" class="mtts-file-input" accept="image/*,video/*" onchange="previewMedia(this); expandPost();" style="display:none;">
                </label>
                <label class="amen-btn" style="cursor:pointer; display:flex; align-items:center; gap:8px; padding: 8px; flex:1; justify-content:center; border-radius:4px;">
                     <span class="dashicons dashicons-smiley" style="color: #F7B928;"></span> 
                     <span style="font-size:14px; color:#65676B; font-weight:600;">Activity</span>
                </label>
            </div>
            <div id="post-submit-container" style="display:none; margin-top:12px;">
                <button type="submit" class="mtts-btn mtts-btn-primary" style="width:100%; border-radius:6px; padding: 8px; font-weight:600;">Post</button>
            </div>
        </form>
    </div>

    <!-- 3. Dynamic Feed Items -->
    <div class="nexus-main-feed" style="max-width: 100%;">
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
                    <button type="button" class="amen-btn <?php echo (isset($post->likes_count) && $post->likes_count > 0) ? 'active' : ''; ?>" onclick="socialAction('amen_post', <?php echo $post->id; ?>, this)">
                        <span class="dashicons dashicons-heart"></span> Amen <?php echo $post->likes_count ?: ''; ?>
                    </button>
                    <button type="button" class="amen-btn" onclick="toggleComments(<?php echo $post->id; ?>)">
                        <span class="dashicons dashicons-admin-comments"></span> Feedback (<?php echo $post->comments_count; ?>)
                    </button>
                </div>
                
                <div id="comments-<?php echo $post->id; ?>" style="display:none; margin-top:20px; border-top:1px solid #f1f5f9; padding-top:20px;"></div>
            </div>
        <?php endforeach; ?>
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
