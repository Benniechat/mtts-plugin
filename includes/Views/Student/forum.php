<?php
/**
 * Student Forum View
 * Supports: post list, single post thread, new post form
 */
$current_user_id = get_current_user_id();
$forum_action    = isset( $_GET['forum'] ) ? sanitize_key( $_GET['forum'] ) : 'list';
$post_id         = isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : 0;
$category_filter = isset( $_GET['cat'] ) ? sanitize_key( $_GET['cat'] ) : null;

// Handle new post submission
if ( isset( $_POST['mtts_forum_action'] ) && $_POST['mtts_forum_action'] === 'new_post' && \MttsLms\Core\Security::check_request( 'mtts_forum_post' ) ) {
    $post_data = \MttsLms\Core\Security::sanitize_deep( $_POST );
    \MttsLms\Models\ForumPost::create( [
        'author_id' => $current_user_id,
        'course_id' => intval( $post_data['course_id'] ?? 0 ) ?: null,
        'category'  => sanitize_key( $post_data['category'] ?? 'general' ),
        'title'     => $post_data['title'],
        'body'      => $post_data['body'],
    ] );
    wp_redirect( remove_query_arg( 'forum', add_query_arg( 'view', 'forum', get_permalink() ) ) );
    exit;
}

// Handle reply submission
if ( isset( $_POST['mtts_forum_action'] ) && $_POST['mtts_forum_action'] === 'reply' && \MttsLms\Core\Security::check_request( 'mtts_forum_reply' ) ) {
    $post_data = \MttsLms\Core\Security::sanitize_deep( $_POST );
    \MttsLms\Models\ForumReply::create( [
        'post_id'   => intval( $post_data['post_id'] ),
        'author_id' => $current_user_id,
        'body'      => $post_data['body'],
    ] );
    wp_redirect( add_query_arg( [ 'view' => 'forum', 'forum' => 'thread', 'post_id' => intval( $post_data['post_id'] ) ], get_permalink() ) );
    exit;
}

$categories = [
    'general'   => '💬 General',
    'academic'  => '📚 Academic',
    'prayer'    => '🙏 Prayer Requests',
    'social'    => '🎉 Social',
];
?>

<div class="mtts-dashboard-section">

    <?php if ( $forum_action === 'new' ) : ?>
        <!-- New Post Form -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>📝 New Discussion Post</h2>
            <a href="?view=forum" class="mtts-btn">← Back to Forum</a>
        </div>
        <div class="mtts-card">
            <form method="post" action="">
                <input type="hidden" name="mtts_forum_action" value="new_post">
                <?php wp_nonce_field( 'mtts_forum_post' ); ?>
                <div class="mtts-form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="mtts-form-control" placeholder="Post title..." required>
                </div>
                <div class="mtts-form-group">
                    <label>Category</label>
                    <select name="category" class="mtts-form-control">
                        <?php foreach ( $categories as $key => $label ) : ?>
                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mtts-form-group">
                    <label>Message</label>
                    <textarea name="body" class="mtts-form-control" rows="6" placeholder="Share your thoughts..." required></textarea>
                </div>
                <button type="submit" class="mtts-btn mtts-btn-primary">Post Discussion</button>
            </form>
        </div>

    <?php elseif ( $forum_action === 'thread' && $post_id ) : ?>
        <!-- Thread View -->
        <?php
        $forum_post = \MttsLms\Models\ForumPost::find( $post_id );
        $replies    = \MttsLms\Models\ForumReply::get_by_post( $post_id );
        $post_author = get_userdata( $forum_post->author_id );
        ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>💬 <?php echo esc_html( $forum_post->title ); ?></h2>
            <a href="?view=forum" class="mtts-btn">← Back to Forum</a>
        </div>

        <!-- Original Post -->
        <div class="mtts-card" style="border-left: 4px solid #7c3aed; margin-bottom:20px;">
            <?php if ( $forum_post->is_pinned ) : ?>
                <span style="background:#7c3aed; color:#fff; padding:2px 10px; border-radius:20px; font-size:0.8rem; margin-bottom:10px; display:inline-block;">📌 Pinned</span>
            <?php endif; ?>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong><?php echo esc_html( $post_author ? $post_author->display_name : 'Unknown' ); ?></strong>
                <small style="color:#999;"><?php echo date( 'M j, Y g:ia', strtotime( $forum_post->created_at ) ); ?></small>
            </div>
            <p><?php echo nl2br( esc_html( $forum_post->body ) ); ?></p>
        </div>

        <!-- Replies -->
        <?php if ( ! empty( $replies ) ) : ?>
            <h4 style="margin-bottom:15px;"><?php echo count( $replies ); ?> Repl<?php echo count( $replies ) === 1 ? 'y' : 'ies'; ?></h4>
            <?php foreach ( $replies as $reply ) :
                $reply_author = get_userdata( $reply->author_id );
                $is_mine = ( $reply->author_id == $current_user_id );
            ?>
                <div style="margin-bottom:15px; padding:15px; background:<?php echo $is_mine ? '#f0f4ff' : '#f9f9f9'; ?>; border-radius:8px; border-left:3px solid <?php echo $is_mine ? '#7c3aed' : '#ddd'; ?>;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <strong><?php echo $is_mine ? 'You' : esc_html( $reply_author ? $reply_author->display_name : 'Unknown' ); ?></strong>
                        <small style="color:#999;"><?php echo date( 'M j, Y g:ia', strtotime( $reply->created_at ) ); ?></small>
                    </div>
                    <p style="margin:0;"><?php echo nl2br( esc_html( $reply->body ) ); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Reply Form -->
        <div class="mtts-card" style="margin-top:20px;">
            <h4>Add a Reply</h4>
            <form method="post" action="">
                <input type="hidden" name="mtts_forum_action" value="reply">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <?php wp_nonce_field( 'mtts_forum_reply' ); ?>
                <textarea name="body" class="mtts-form-control" rows="4" placeholder="Write your reply..." required></textarea>
                <button type="submit" class="mtts-btn mtts-btn-primary" style="margin-top:10px;">Post Reply</button>
            </form>
        </div>

    <?php else : ?>
        <!-- Post List -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>🗣️ Discussion Forum</h2>
            <a href="?view=forum&forum=new" class="mtts-btn mtts-btn-primary">+ New Post</a>
        </div>

        <!-- Category Filter -->
        <div style="margin-bottom:15px; display:flex; gap:8px; flex-wrap:wrap;">
            <a href="?view=forum" class="mtts-btn <?php echo ! $category_filter ? 'mtts-btn-primary' : ''; ?>">All</a>
            <?php foreach ( $categories as $key => $label ) : ?>
                <a href="?view=forum&cat=<?php echo $key; ?>" class="mtts-btn <?php echo $category_filter === $key ? 'mtts-btn-primary' : ''; ?>"><?php echo $label; ?></a>
            <?php endforeach; ?>
        </div>

        <?php $posts = \MttsLms\Models\ForumPost::get_all( $category_filter ); ?>

        <div class="mtts-card">
            <?php if ( empty( $posts ) ) : ?>
                <p style="text-align:center; color:#999; padding:30px;">No discussions yet. Be the first to start one!</p>
            <?php else : ?>
                <?php foreach ( $posts as $fp ) :
                    $fp_author     = get_userdata( $fp->author_id );
                    $reply_count   = \MttsLms\Models\ForumPost::get_reply_count( $fp->id );
                    $cat_label     = $categories[ $fp->category ] ?? $fp->category;
                ?>
                    <div style="padding:15px; border-bottom:1px solid #eee; <?php echo $fp->is_pinned ? 'background:#faf5ff;' : ''; ?>">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <?php if ( $fp->is_pinned ) : ?><span style="background:#7c3aed; color:#fff; padding:1px 8px; border-radius:20px; font-size:0.75rem; margin-right:6px;">📌</span><?php endif; ?>
                                <a href="?view=forum&forum=thread&post_id=<?php echo $fp->id; ?>" style="font-weight:bold; font-size:1.05rem; color:#1a1a2e; text-decoration:none;">
                                    <?php echo esc_html( $fp->title ); ?>
                                </a>
                                <span style="background:#eee; padding:1px 8px; border-radius:20px; font-size:0.75rem; margin-left:8px;"><?php echo $cat_label; ?></span>
                            </div>
                            <small style="color:#999; white-space:nowrap;"><?php echo date( 'M j', strtotime( $fp->created_at ) ); ?></small>
                        </div>
                        <div style="margin-top:6px; font-size:0.9rem; color:#666;">
                            By <strong><?php echo esc_html( $fp_author ? $fp_author->display_name : 'Unknown' ); ?></strong>
                            &nbsp;·&nbsp; 💬 <?php echo $reply_count; ?> repl<?php echo $reply_count === 1 ? 'y' : 'ies'; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
