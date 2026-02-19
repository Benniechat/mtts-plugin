<?php
// friends.php - Alumni Friends View
?>
<div class="mtts-dashboard-section">
    <h2>My Friends</h2>

    <?php if ( !empty( $pending_requests ) ) : ?>
        <div class="mtts-card" style="margin-bottom: 20px;">
            <h3>Pending Friend Requests (<?php echo count($pending_requests); ?>)</h3>
            <div class="mtts-friend-requests">
                <?php foreach ( $pending_requests as $req ) : 
                    $sender = get_userdata( $req->sender_id );
                ?>
                    <div class="mtts-friend-request-item" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-bottom: 1px solid #eee;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="<?php echo get_avatar_url( $req->sender_id ); ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%;">
                            <div>
                                <strong><?php echo esc_html( $sender->display_name ); ?></strong>
                                <br><small><?php echo esc_html( $sender->user_email ); ?></small>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <form method="post" action="" style="display: inline;">
                                <input type="hidden" name="mtts_action" value="accept_request">
                                <input type="hidden" name="request_id" value="<?php echo $req->id; ?>">
                                <?php wp_nonce_field( 'mtts_accept_friend_request' ); ?>
                                <button type="submit" class="button button-primary button-small">Accept</button>
                            </form>
                            <form method="post" action="" style="display: inline;">
                                <input type="hidden" name="mtts_action" value="reject_request">
                                <input type="hidden" name="request_id" value="<?php echo $req->id; ?>">
                                <?php wp_nonce_field( 'mtts_reject_friend_request' ); ?>
                                <button type="submit" class="button button-small">Reject</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="mtts-card">
        <h3>Friends List (<?php echo count($friends_data); ?>)</h3>
        <?php if ( empty( $friends_data ) ) : ?>
            <p>You haven't connected with any alumni yet. Visit the <a href="?view=directory">Alumni Directory</a> to find friends!</p>
        <?php else : ?>
            <div class="mtts-friends-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                <?php foreach ( $friends_data as $friend_rel ) : 
                    $friend_id = ($friend_rel->sender_id == $user->ID) ? $friend_rel->receiver_id : $friend_rel->sender_id;
                    $friend = get_userdata( $friend_id );
                ?>
                    <div class="mtts-friend-card" style="text-align: center; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                        <img src="<?php echo get_avatar_url( $friend_id ); ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px;">
                        <h4 style="margin: 0;"><?php echo esc_html( $friend->display_name ); ?></h4>
                        <small><?php echo esc_html( $friend->user_email ); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
