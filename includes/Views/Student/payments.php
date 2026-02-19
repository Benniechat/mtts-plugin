<h2>Fee Payments</h2>

<div class="mtts-card">
    <h3>Make a Payment</h3>
    <form method="post" action="">
        <input type="hidden" name="mtts_action" value="initiate_payment">
        <?php wp_nonce_field( 'mtts_initiate_payment' ); ?>
        
        <div class="mtts-form-group">
            <label for="payment_purpose">Payment For</label>
            <select name="payment_purpose" id="payment_purpose" class="mtts-form-control">
                <option value="tuition">Tuition Fee</option>
                <option value="acceptance">Acceptance Fee</option>
                <option value="id_card">ID Card Request</option>
            </select>
        </div>

        <div class="mtts-form-group">
            <label for="amount">Amount (NGN)</label>
            <input type="number" name="amount" id="amount" class="mtts-form-control" required min="100">
        </div>

        <div class="mtts-form-group">
            <label>Payment Method</label>
            <select name="payment_method" class="mtts-form-control">
                <option value="paystack">Paystack</option>
                <option value="flutterwave">Flutterwave</option>
                <option value="wallet">Wallet Balance (&#8358;<?php echo number_format( \MttsLms\Models\Wallet::get_by_student( $student->id )->balance, 2 ); ?>)</option>
            </select>
        </div>

        <div class="mtts-form-group">
            <button type="submit" name="mtts_pay_now" class="mtts-btn mtts-btn-primary">Pay Now</button>
        </div>
    </form>
</div>

<div class="mtts-card" style="margin-top: 30px;">
    <h3>Payment History</h3>
    <table class="mtts-table-list">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Purpose</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $transactions ) ) : ?>
                <?php foreach ( $transactions as $trx ) : ?>
                    <tr>
                        <td><?php echo esc_html( $trx->reference ); ?></td>
                        <td><?php echo esc_html( ucfirst( $trx->purpose ) ); ?></td>
                        <td>₦<?php echo esc_html( number_format( $trx->amount, 2 ) ); ?></td>
                        <td><?php echo esc_html( $trx->created_at ); ?></td>
                        <td>
                            <span class="mtts-status mtts-status-<?php echo esc_attr( $trx->status ); ?>">
                                <?php echo esc_html( ucfirst( $trx->status ) ); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5">No transactions found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
