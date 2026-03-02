<div class="wrap">
    <h1>Social Feed Moderation</h1>
    <p>Monitor and moderate posts from the alumni network.</p>

    <div class="mtts-moderation-filters" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <a href="#" class="button button-primary">All Posts</a>
        <a href="#" class="button">Flagged</a>
        <a href="#" class="button">Pending Approval</a>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Author</th>
                <th>Content</th>
                <th>Type</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $posts = \MttsLms\Models\AlumniPost::get_feed( 10 );
            if ( ! empty( $posts ) ) :
                foreach ( $posts as $post ) :
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $post->author_name ); ?></strong></td>
                        <td><?php echo wp_trim_words( $post->content, 10 ); ?></td>
                        <td><?php echo ucfirst( $post->type ); ?></td>
                        <td><?php echo esc_html( $post->created_at ); ?></td>
                        <td><span class="badge" style="background: #10b981; color: #fff; padding: 2px 8px; border-radius: 4px;">Published</span></td>
                        <td>
                            <a href="#" class="button button-small">View</a>
                            <a href="#" class="button button-small" style="color: #ef4444;">Delete</a>
                            <a href="#" class="button button-small">Flag</a>
                        </td>
                    </tr>
                    <?php
                endforeach;
            else :
                ?>
                <tr><td colspan="6">No posts found to moderate.</td></tr>
                <?php
            endif;
            ?>
        </tbody>
    </table>
</div>
