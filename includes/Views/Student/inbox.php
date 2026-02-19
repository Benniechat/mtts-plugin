<?php
// includes/Views/Student/inbox.php
$current_user_id = get_current_user_id();
$view_mode = isset( $_GET['msg'] ) ? 'thread' : ( isset( $_GET['compose'] ) ? 'compose' : 'inbox' );
$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'inbox';
?>
<div class="mtts-dashboard-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>📬 Messages</h2>
        <a href="?view=inbox&compose=1" class="mtts-btn mtts-btn-primary">✏️ Compose</a>
    </div>

    <?php if ( $view_mode === 'compose' ) : ?>
        <!-- Compose Form -->
        <div class="mtts-card">
            <h3>New Message</h3>
            <form method="post" action="">
                <input type="hidden" name="mtts_action" value="send_message">
                <?php wp_nonce_field( 'mtts_send_message' ); ?>
                <div class="mtts-form-group">
                    <label>To (User ID or Email)</label>
                    <input type="text" name="receiver_search" class="mtts-form-control" placeholder="Enter lecturer name or email" required>
                    <input type="hidden" name="receiver_id" id="receiver_id_field">
                </div>
                <div class="mtts-form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" class="mtts-form-control" placeholder="Message subject" required>
                </div>
                <div class="mtts-form-group">
                    <label>Message</label>
                    <textarea name="body" class="mtts-form-control" rows="6" placeholder="Write your message here..." required></textarea>
                </div>
                <div class="mtts-form-group">
                    <label>Send To</label>
                    <select name="receiver_id" class="mtts-form-control" required>
                        <option value="">-- Select Recipient --</option>
                        <?php
                        $lecturers = get_users( [ 'role' => 'mtts_lecturer' ] );
                        foreach ( $lecturers as $l ) :
                        ?>
                            <option value="<?php echo $l->ID; ?>"><?php echo esc_html( $l->display_name ); ?> (Lecturer)</option>
                        <?php endforeach; ?>
                        <?php
                        $admins = get_users( [ 'role' => 'mtts_admin' ] );
                        foreach ( $admins as $a ) :
                        ?>
                            <option value="<?php echo $a->ID; ?>"><?php echo esc_html( $a->display_name ); ?> (Admin)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="mtts-btn mtts-btn-primary">Send Message</button>
                <a href="?view=inbox" class="mtts-btn" style="margin-left:10px;">Cancel</a>
            </form>
        </div>

    <?php elseif ( $view_mode === 'thread' ) : ?>
        <!-- Thread View -->
        <?php
        $message_id = intval( $_GET['msg'] );
        $thread = \MttsLms\Models\Message::get_thread( $message_id );
        \MttsLms\Models\Message::mark_read( $message_id );
        $root = $thread[0] ?? null;
        ?>
        <div class="mtts-card">
            <a href="?view=inbox" style="font-size:0.9rem;">← Back to Inbox</a>
            <h3 style="margin-top:15px;"><?php echo esc_html( $root->subject ?? 'No Subject' ); ?></h3>
            <?php foreach ( $thread as $msg ) :
                $is_mine = ( $msg->sender_id == $current_user_id );
                $other_user = get_userdata( $is_mine ? $msg->receiver_id : $msg->sender_id );
            ?>
                <div style="margin: 15px 0; padding: 15px; background: <?php echo $is_mine ? '#f0f4ff' : '#f9f9f9'; ?>; border-radius: 8px; border-left: 4px solid <?php echo $is_mine ? '#7c3aed' : '#ddd'; ?>;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <strong><?php echo $is_mine ? 'You' : esc_html( $other_user->display_name ); ?></strong>
                        <small style="color:#999;"><?php echo date( 'M j, Y g:ia', strtotime( $msg->created_at ) ); ?></small>
                    </div>
                    <p style="margin:0;"><?php echo nl2br( esc_html( $msg->body ) ); ?></p>
                </div>
            <?php endforeach; ?>

            <!-- Reply Form -->
            <div style="margin-top:20px; padding-top:20px; border-top: 1px solid #eee;">
                <h4>Reply</h4>
                <form method="post" action="">
                    <input type="hidden" name="mtts_action" value="send_message">
                    <input type="hidden" name="receiver_id" value="<?php echo $is_mine ? $root->receiver_id : $root->sender_id; ?>">
                    <input type="hidden" name="subject" value="Re: <?php echo esc_attr( $root->subject ?? '' ); ?>">
                    <input type="hidden" name="parent_id" value="<?php echo $message_id; ?>">
                    <?php wp_nonce_field( 'mtts_send_message' ); ?>
                    <textarea name="body" class="mtts-form-control" rows="4" placeholder="Write your reply..." required></textarea>
                    <button type="submit" class="mtts-btn mtts-btn-primary" style="margin-top:10px;">Send Reply</button>
                </form>
            </div>
        </div>

    <?php else : ?>
        <!-- Inbox / Sent Tabs -->
        <div style="margin-bottom: 15px;">
            <a href="?view=inbox&tab=inbox" class="mtts-btn <?php echo $active_tab === 'inbox' ? 'mtts-btn-primary' : ''; ?>">Inbox
                <?php $unread = \MttsLms\Models\Message::count_unread( $current_user_id ); if ( $unread > 0 ) : ?>
                    <span style="background:#e53e3e; color:white; border-radius:50%; padding:2px 7px; font-size:0.75rem; margin-left:5px;"><?php echo $unread; ?></span>
                <?php endif; ?>
            </a>
            <a href="?view=inbox&tab=sent" class="mtts-btn <?php echo $active_tab === 'sent' ? 'mtts-btn-primary' : ''; ?>" style="margin-left:8px;">Sent</a>
        </div>

        <?php
        $messages = ( $active_tab === 'sent' )
            ? \MttsLms\Models\Message::get_sent( $current_user_id )
            : \MttsLms\Models\Message::get_inbox( $current_user_id );
        ?>

        <div class="mtts-card">
            <?php if ( empty( $messages ) ) : ?>
                <p style="text-align:center; color:#999; padding:30px;">No messages yet.</p>
            <?php else : ?>
                <table class="mtts-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th><?php echo $active_tab === 'sent' ? 'To' : 'From'; ?></th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $messages as $msg ) :
                            $other_id = ( $active_tab === 'sent' ) ? $msg->receiver_id : $msg->sender_id;
                            $other = get_userdata( $other_id );
                            $is_unread = ( $active_tab === 'inbox' && ! $msg->is_read );
                        ?>
                            <tr style="<?php echo $is_unread ? 'font-weight:bold; background:#fafafa;' : ''; ?>">
                                <td><?php echo esc_html( $other ? $other->display_name : 'Unknown' ); ?></td>
                                <td>
                                    <?php if ( $is_unread ) : ?><span style="color:#7c3aed;">● </span><?php endif; ?>
                                    <?php echo esc_html( $msg->subject ?: '(No Subject)' ); ?>
                                </td>
                                <td style="color:#999; font-size:0.9rem;"><?php echo date( 'M j, Y', strtotime( $msg->created_at ) ); ?></td>
                                <td><a href="?view=inbox&msg=<?php echo $msg->id; ?>" class="mtts-btn mtts-btn-small">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
