<?php
/**
 * Alumni Covenant Feed (Theological Social Hybrid)
 * Mix of FB (Ministry Updates), LinkedIn (Professional), and Twitter (Theological Nuggets)
 */
?>
<style>
    :root {
        --mtts-purple: #4b0082;
        --mtts-gold: #ffd700;
        --mtts-glass: rgba(255, 255, 255, 0.7);
    }
    .covenant-feed-wrapper {
        max-width: 700px;
        margin: 0 auto;
        font-family: 'Inter', sans-serif;
    }
    .glass-card {
        background: var(--mtts-glass);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.07);
        margin-bottom: 25px;
        transition: transform 0.2s ease;
    }
    .glass-card:hover {
        transform: translateY(-2px);
    }
    .post-type-toggle {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }
    .type-btn {
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #ddd;
        background: #fff;
        transition: all 0.3s ease;
    }
    .type-btn.active {
        background: var(--mtts-purple);
        color: #fff;
        border-color: var(--mtts-purple);
    }
    .nugget-indicator {
        font-size: 0.8rem;
        color: #666;
        text-align: right;
        margin-top: 5px;
    }
    .amen-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        color: #666;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    .amen-btn.liked {
        color: var(--mtts-purple);
    }
    .amen-btn:hover {
        color: var(--mtts-purple);
    }
    .post-badge {
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
        text-transform: uppercase;
        font-weight: bold;
        margin-left: 10px;
    }
    .badge-nugget { background: #fee2e2; color: #ef4444; }
    .badge-social { background: #e0e7ff; color: #4338ca; }
</style>

<div class="mtts-dashboard-section covenant-feed-wrapper">
    
    <!-- Create Reflection Box -->
    <div class="glass-card" style="padding: 20px;">
        <div style="display:flex; gap:15px; align-items:flex-start;">
            <img src="<?php echo get_avatar_url( get_current_user_id() ); ?>" style="width:45px; height:45px; border-radius:12px; border: 2px solid var(--mtts-gold);" alt="Me">
            <form method="post" action="" style="flex:1;" id="alumni-post-form">
                <input type="hidden" name="mtts_alumni_action" value="create_post">
                <input type="hidden" name="type" id="post-type-field" value="nugget">
                <?php wp_nonce_field( 'mtts_alumni_social' ); ?>
                
                <div class="post-type-toggle">
                    <div class="type-btn active" onclick="setPostType('nugget', this)">Theological Nugget</div>
                    <div class="type-btn" onclick="setPostType('social', this)">Ministry Update</div>
                </div>

                <textarea name="content" id="post-content" class="mtts-form-control" placeholder="Share a theological reflection..." style="border:1px solid #eee; background:rgba(255,255,255,0.5); border-radius:12px; padding:15px; font-size:1rem; resize:none;" rows="2" required></textarea>
                
                <div id="char-counter" class="nugget-indicator">280 characters remaining</div>
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; border-top:1px solid rgba(0,0,0,0.05); padding-top:10px;">
                    <div style="display:flex; gap:12px; color:#666; font-size:0.85rem;">
                        <span style="cursor:pointer;"><span class="dashicons dashicons-format-image"></span> Media</span>
                        <span style="cursor:pointer;"><span class="dashicons dashicons-admin-links"></span> Scripture</span>
                    </div>
                    <button type="submit" class="mtts-btn" style="background:var(--mtts-purple); color:#fff; padding: 8px 20px; border-radius:12px; font-weight:600;">Release</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Feed List -->
    <?php if ( empty( $posts ) ) : ?>
        <div class="glass-card" style="text-align:center; color:#999; padding:60px;">
            <span class="dashicons dashicons-format-quote" style="font-size:3.5rem; width:auto; height:auto; color:var(--mtts-gold); opacity:0.5;"></span>
            <p style="font-size:1.1rem; margin-top:15px;">The Covenant Feed is empty.<br>Start the conversation by sharing a reflection.</p>
        </div>
    <?php else : ?>
        <?php foreach ( $posts as $post ) : 
            $author_profile = \MttsLms\Models\AlumniProfile::get_by_user( $post->author_id );
            $is_nugget = ($post->type === 'nugget');
        ?>
            <div class="glass-card" style="padding: 0; overflow:hidden;">
                <!-- Post Header -->
                <div style="padding:15px 20px; display:flex; gap:12px; align-items:center; border-bottom: 1px solid rgba(0,0,0,0.02);">
                    <img src="<?php echo get_avatar_url( $post->author_id ); ?>" style="width:40px; height:40px; border-radius:10px;" alt="Author">
                    <div style="flex:1;">
                        <div style="font-weight:700; color:var(--mtts-purple); display:flex; align-items:center;">
                            <?php echo esc_html( $post->display_name ); ?>
                            <span class="post-badge badge-<?php echo $post->type; ?>"><?php echo $is_nugget ? 'Nugget' : 'Update'; ?></span>
                        </div>
                        <div style="color:#777; font-size:0.75rem;">
                            <?php echo esc_html( $author_profile->headline ?: 'Theologian' ); ?> · 
                            <?php echo human_time_diff( strtotime( $post->created_at ), current_time( 'timestamp' ) ); ?> ago
                        </div>
                    </div>
                </div>

                <!-- Post Content -->
                <div style="padding:20px; font-size:<?php echo $is_nugget ? '1.2rem' : '1rem'; ?>; line-height:1.6; color:#2d3748; <?php echo $is_nugget ? 'font-style: italic; font-family: serif;' : ''; ?>">
                    <?php echo nl2br( esc_html( $post->content ) ); ?>
                </div>

                <!-- Footer Interactions -->
                <div style="padding:12px 20px; display:flex; gap:25px; border-top:1px solid rgba(0,0,0,0.03); background: rgba(255,255,255,0.3);">
                    <div class="amen-btn" onclick="amenPost(<?php echo $post->id; ?>, this)">
                        <span class="dashicons dashicons-heart" style="font-size:1.1rem; margin-top:2px;"></span>
                        <span class="count"><?php echo $post->likes_count ?: 'Amen'; ?></span>
                    </div>
                    <div class="amen-btn" onclick="toggleComments(<?php echo $post->id; ?>)">
                        <span class="dashicons dashicons-admin-comments" style="font-size:1.1rem; margin-top:2px;"></span>
                        <span>Discern</span>
                    </div>
                    <div style="flex:1; text-align:right;">
                        <span class="dashicons dashicons-share-alt2" style="color:#aaa; cursor:pointer;"></span>
                    </div>
                </div>

                <!-- Comments Area (Discernment) -->
                <div id="comments-<?php echo $post->id; ?>" class="discernment-area" style="display:none; padding:15px 20px; background: rgba(0,0,0,0.01); border-top:1px solid rgba(0,0,0,0.03);">
                    <?php 
                    $comments = \MttsLms\Models\AlumniComment::get_by_post( $post->id );
                    if ( $comments ) : ?>
                        <div class="comments-list" style="margin-bottom:15px;">
                            <?php foreach ( $comments as $comment ) : ?>
                                <div style="display:flex; gap:10px; margin-bottom:12px;">
                                    <img src="<?php echo get_avatar_url( $comment->author_id ); ?>" style="width:28px; height:28px; border-radius:6px;" alt="Avatar">
                                    <div style="background:rgba(255,255,255,0.8); padding:8px 12px; border-radius:12px; font-size:0.85rem; flex:1;">
                                        <div style="font-weight:700; color:var(--mtts-purple); font-size:0.75rem;"><?php echo esc_html( $comment->display_name ); ?></div>
                                        <div style="color:#333;"><?php echo nl2br( esc_html( $comment->content ) ); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form onsubmit="postComment(event, <?php echo $post->id; ?>)" style="display:flex; gap:10px;">
                        <input type="text" class="mtts-form-control discern-input" placeholder="Share your discernment..." style="font-size:0.85rem; border-radius:10px; padding:8px 12px; flex:1;">
                        <button type="submit" class="mtts-btn" style="padding:4px 12px; font-size:0.8rem; background:var(--mtts-gold); color:var(--mtts-purple); font-weight:bold; border-radius:8px;">Send</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script>
function setPostType(type, el) {
    document.getElementById('post-type-field').value = type;
    document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    
    const content = document.getElementById('post-content');
    const counter = document.getElementById('char-counter');
    
    if (type === 'nugget') {
        content.placeholder = "Share a theological reflection...";
        content.rows = 2;
        counter.style.display = 'block';
    } else {
        content.placeholder = "What's the latest in your mission field?";
        content.rows = 4;
        counter.style.display = 'none';
    }
}

document.getElementById('post-content').addEventListener('input', function() {
    const type = document.getElementById('post-type-field').value;
    if (type === 'nugget') {
        const remaining = 280 - this.value.length;
        const counter = document.getElementById('char-counter');
        counter.innerText = remaining + ' characters remaining';
        if (remaining < 0) {
            counter.style.color = '#ef4444';
        } else {
            counter.style.color = '#666';
        }
    }
});

function amenPost(postId, el) {
    const formData = new FormData();
    formData.append('mtts_alumni_action', 'like_post');
    formData.append('post_id', postId);
    formData.append('_wpnonce', '<?php echo wp_create_nonce("mtts_alumni_social"); ?>');

    el.classList.add('liked');
    fetch('', {
        method: 'POST',
        body: formData
    }).then(() => {
        const countEl = el.querySelector('.count');
        if (countEl.innerText === 'Amen') {
            countEl.innerText = '1';
        } else {
            countEl.innerText = parseInt(countEl.innerText) + 1;
        }
    });
}

function toggleComments(postId) {
    const area = document.getElementById('comments-' + postId);
    area.style.display = area.style.display === 'none' ? 'block' : 'none';
}

function postComment(event, postId) {
    event.preventDefault();
    const form = event.target;
    const input = form.querySelector('.discern-input');
    const content = input.value;
    
    if (!content) return;

    const formData = new FormData();
    formData.append('mtts_alumni_action', 'add_comment');
    formData.append('post_id', postId);
    formData.append('content', content);
    formData.append('_wpnonce', '<?php echo wp_create_nonce("mtts_alumni_social"); ?>');

    fetch('', {
        method: 'POST',
        body: formData
    }).then(() => {
        location.reload(); 
    });
}
</script>
