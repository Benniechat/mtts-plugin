<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$friends = \MttsLms\Models\FriendRequest::get_friends( $user->ID );
$active_chat_id = isset( $_GET['chat_with'] ) ? intval( $_GET['chat_with'] ) : null;
$messages = $active_chat_id ? \MttsLms\Models\Message::get_thread( $active_chat_id ) : array(); // Thread logic might need refinement relative to user_id
?>

<div class="mtts-dashboard-section" style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns:300px 1fr; gap:2px; height:700px; background:#fff; border-radius:15px; overflow:hidden; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
    
    <!-- Sidebar: Conversations -->
    <div style="background:#f8fafc; border-right:1px solid #e2e8f0; display:flex; flex-direction:column;">
        <div style="padding:20px; border-bottom:1px solid #e2e8f0;">
            <h3 style="margin:0; font-size:20px;" class="spiritual-gradient-text">Koinonia Inbox</h3>
        </div>
        <div style="flex:1; overflow-y:auto; padding:10px;">
            <?php if ( empty( $friends ) ) : ?>
                <p style="text-align:center; padding:20px; color:#64748b; font-size:14px;">Connect with ministers to start chatting.</p>
            <?php else : ?>
                <?php foreach ( $friends as $request ) : 
                    $friend_id = ( $request->sender_id == $user->ID ) ? $request->receiver_id : $request->sender_id;
                    $friend = get_userdata( $friend_id );
                    $is_active = ( $active_chat_id == $friend_id );
                ?>
                    <a href="?view=messenger&chat_with=<?php echo $friend_id; ?>" style="display:flex; align-items:center; padding:12px; border-radius:10px; text-decoration:none; margin-bottom:5px; background:<?php echo $is_active ? '#eff6ff' : 'transparent'; ?>; transition:all 0.2s;">
                        <img src="<?php echo get_avatar_url( $friend_id ); ?>" style="width:45px; height:45px; border-radius:50%; margin-right:12px;" alt="">
                        <div>
                            <div style="font-weight:700; color:#1e293b; font-size:14px;"><?php echo esc_html( $friend->display_name ); ?></div>
                            <div style="font-size:12px; color:#64748b;">Tap to chat...</div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Chat Area -->
    <div style="display:flex; flex-direction:column; background:#fff;">
        <?php if ( $active_chat_id ) : 
            $chat_partner = get_userdata( $active_chat_id );
        ?>
            <!-- Chat Header -->
            <div style="padding:15px 25px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.8); backdrop-filter:blur(10px);">
                <div style="display:flex; align-items:center;">
                    <img src="<?php echo get_avatar_url( $active_chat_id ); ?>" style="width:40px; height:40px; border-radius:50%; margin-right:12px;" alt="">
                    <div>
                        <div style="font-weight:700; color:#1e293b;"><?php echo esc_html( $chat_partner->display_name ); ?></div>
                        <div style="font-size:11px; color:#10b981;">● Active in Fellowship</div>
                    </div>
                </div>
                <div style="background:#f1f5f9; padding:5px 12px; border-radius:15px; font-size:11px; color:#64748b; display:flex; align-items:center; gap:5px;">
                    <span class="dashicons dashicons-lock" style="font-size:14px; width:auto; height:auto;"></span> End-to-End Encrypted
                </div>
            </div>

            <!-- Messages Area -->
            <div id="mtts-chat-messages" style="flex:1; overflow-y:auto; padding:25px; display:flex; flex-direction:column; gap:15px; background:url('https://www.transparenttextures.com/patterns/cubes.png');">
                <?php 
                // Enhanced Message Fetching: Both sent/received in this thread
                global $wpdb;
                $table = $wpdb->prefix . 'mtts_messages';
                $thread = $wpdb->get_results( $wpdb->prepare(
                    "SELECT * FROM {$table} 
                     WHERE (sender_id = %d AND receiver_id = %d) 
                        OR (sender_id = %d AND receiver_id = %d) 
                     ORDER BY created_at ASC",
                    $user->ID, $active_chat_id, $active_chat_id, $user->ID
                ) );

                if ( empty( $thread ) ) : ?>
                    <div style="text-align:center; padding:40px; color:#94a3b8;">
                        <span class="dashicons dashicons-format-chat" style="font-size:3rem; width:auto; height:auto; opacity:0.2;"></span>
                        <p style="margin-top:15px;">Start a holy conversation with <?php echo esc_html( $chat_partner->first_name ?: $chat_partner->display_name ); ?>.</p>
                    </div>
                <?php else : 
                    foreach ( $thread as $msg ) : 
                        $is_me = ( $msg->sender_id == $user->ID );
                        $body = $msg->is_encrypted ? \MttsLms\Models\Message::decrypt_body( $msg->body ) : $msg->body;
                ?>
                    <div style="display:flex; justify-content:<?php echo $is_me ? 'flex-end' : 'flex-start'; ?>;">
                        <div style="max-width:70%; padding:12px 18px; border-radius:18px; position:relative; <?php 
                            echo $is_me 
                            ? 'background:linear-gradient(135deg, #7c3aed, #6d28d9); color:#fff; border-bottom-right-radius:2px;' 
                            : 'background:#f1f5f9; color:#1e293b; border-bottom-left-radius:2px;'; 
                        ?>">
                            <div style="font-size:14px; line-height:1.5;"><?php echo nl2br( esc_html( $body ) ); ?></div>
                            <div style="font-size:9px; margin-top:5px; opacity:0.7; text-align:right;">
                                <?php echo date( 'H:i', strtotime( $msg->created_at ) ); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <!-- Input Area -->
            <div style="padding:20px; border-top:1px solid #e2e8f0; background:#fff;">
                <form id="mtts-messenger-form" style="display:flex; gap:10px;">
                    <textarea id="chat-body" placeholder="Propagate a word..." style="flex:1; padding:12px 20px; border-radius:25px; border:1px solid #e2e8f0; resize:none; height:45px; background:#f8fafc; font-size:14px;"></textarea>
                    <button type="submit" class="mtts-btn mtts-btn-primary" style="width:45px; height:45px; border-radius:50%; display:flex; align-items:center; justify-content:center; padding:0;">
                         <span class="dashicons dashicons-paper-plane"></span>
                    </button>
                    <input type="hidden" id="chat-receiver-id" value="<?php echo $active_chat_id; ?>">
                    <?php wp_nonce_field( 'mtts_alumni_social' ); ?>
                </form>
            </div>

            <script>
            jQuery(document).ready(function($) {
                const scrollChat = () => {
                    const chat = $('#mtts-chat-messages');
                    chat.scrollTop(chat[0].scrollHeight);
                };
                scrollChat();

                $('#mtts-messenger-form').on('submit', function(e) {
                    e.preventDefault();
                    const body = $('#chat-body').val();
                    const receiverId = $('#chat-receiver-id').val();
                    if (!body.trim()) return;

                    $.post(mttsLms.ajaxurl, {
                        action: 'mtts_alumni_social_action',
                        mtts_alumni_action: 'send_private_message',
                        receiver_id: receiverId,
                        body: body,
                        _wpnonce: $('#_wpnonce').val()
                    }, function(res) {
                        if (res.success) {
                            location.reload(); // Quick fix for real-time appearance
                        }
                    });
                });
            });
            </script>

        <?php else : ?>
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc; color:#94a3b8;">
                <span class="dashicons dashicons-welcome-widgets-menus" style="font-size:5rem; width:auto; height:auto; opacity:0.1; margin-bottom:20px;"></span>
                <h3 style="margin:0;">Select a Fellowship Dialogue</h3>
                <p>Private encrypted messages between ministers.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
