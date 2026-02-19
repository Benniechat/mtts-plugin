<div class="mtts-dashboard-section">
    <h2>My Wallet</h2>

    <div class="mtts-card" style="background: linear-gradient(135deg, #4b0082 0%, #ffd700 100%); color: #fff; text-align: center; padding: 40px;">
        <span style="font-size: 1.2rem; opacity: 0.9;">Available Balance</span>
        <div style="font-size: 3rem; font-weight: bold; margin: 10px 0;">
            &#8358;<?php echo number_format( $balance, 2 ); ?>
        </div>
        <button class="mtts-btn" style="background: #fff; color: #4b0082;" onclick="document.getElementById('mtts-fund-modal').style.display='block'">Fund Wallet</button>
        <button class="mtts-btn" style="background: rgba(255,255,255,0.2); color: #fff; border: 1px solid #fff; margin-left: 10px;" onclick="document.getElementById('mtts-transfer-modal').style.display='block'">Transfer Funds</button>
    </div>

    <h3>Transaction History</h3>
    <table class="mtts-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $history ) ) : ?>
                <tr>
                    <td colspan="5">No transactions yet.</td>
                </tr>
            <?php else : ?>
                <?php foreach ( $history as $trx ) : ?>
                    <tr>
                        <td><?php echo date( 'M j, Y H:i', strtotime( $trx->created_at ) ); ?></td>
                        <td><?php echo esc_html( $trx->description ); ?></td>
                        <td>
                            <span class="mtts-badge mtts-badge-<?php echo $trx->type == 'credit' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst( $trx->type ); ?>
                            </span>
                        </td>
                        <td>&#8358;<?php echo number_format( $trx->amount, 2 ); ?></td>
                        <td><?php echo esc_html( $trx->reference ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Fund Wallet Modal -->
<div id="mtts-fund-modal" class="mtts-modal" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="mtts-modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 400px; border-radius: 8px;">
        <span onclick="document.getElementById('mtts-fund-modal').style.display='none'" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h3>Fund Wallet</h3>
        <form method="post" action="">
            <?php wp_nonce_field( 'mtts_fund_wallet' ); ?>
            <div class="mtts-form-group">
                <label>Amount (&#8358;)</label>
                <input type="number" name="amount" min="100" class="mtts-form-control" required>
            </div>
            <div class="mtts-form-group">
                <label>Payment Gateway</label>
                <select name="gateway" class="mtts-form-control">
                    <option value="paystack">Paystack</option>
                    <option value="flutterwave">Flutterwave</option>
                </select>
            </div>
            <button type="submit" name="mtts_fund_wallet" class="mtts-btn mtts-btn-primary" style="width: 100%;">Proceed to Payment</button>
        </form>
    </div>
</div>

<!-- Transfer Funds Modal -->
<div id="mtts-transfer-modal" class="mtts-modal" style="display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="mtts-modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 400px; border-radius: 8px;">
        <span onclick="document.getElementById('mtts-transfer-modal').style.display='none'" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <h3>Transfer Funds</h3>
        <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">Send money to another student's wallet instantly.</p>
        <form method="post" action="" id="mtts-transfer-form">
            <?php wp_nonce_field( 'mtts_transfer_funds' ); ?>
            <div class="mtts-form-group">
                <label>Receiver Matric Number</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="receiver_matric" id="receiver_matric" class="mtts-form-control" placeholder="MTTS/YYYY/XXX/###" required>
                    <button type="button" id="verify-receiver-btn" class="mtts-btn" style="background: #4b0082; color: #fff; padding: 0 15px;">Verify</button>
                </div>
                <div id="receiver-name-display" style="margin-top: 10px; font-weight: bold; color: #4b0082; display: none;">
                    Receiver: <span id="receiver-name"></span>
                </div>
            </div>
            <div class="mtts-form-group">
                <label>Amount (&#8358;)</label>
                <input type="number" name="amount" min="1" step="0.01" class="mtts-form-control" placeholder="0.00" required>
            </div>
            <div class="mtts-form-group">
                <label>Description (Optional)</label>
                <input type="text" name="description" class="mtts-form-control" placeholder="e.g. For study materials">
            </div>
            <div id="transfer-confirm-msg" style="margin-bottom: 20px; font-size: 0.85rem; color: #d9534f; display: none;">
                Please ensure the receiver name above is correct before sent.
            </div>
            <button type="submit" name="mtts_transfer_funds" id="submit-transfer-btn" class="mtts-btn mtts-btn-primary" style="width: 100%;" disabled>Verify Receiver First</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const matricInput = document.getElementById('receiver_matric');
    const verifyBtn = document.getElementById('verify-receiver-btn');
    const nameDisplay = document.getElementById('receiver-name-display');
    const nameSpan = document.getElementById('receiver-name');
    const submitBtn = document.getElementById('submit-transfer-btn');
    const confirmMsg = document.getElementById('transfer-confirm-msg');

    verifyBtn.addEventListener('click', function() {
        const matric = matricInput.value.trim();
        if (!matric) {
            alert('Please enter a matric number.');
            return;
        }

        verifyBtn.innerText = 'Verifying...';
        verifyBtn.disabled = true;

        const formData = new FormData();
        formData.append('action', 'mtts_verify_receiver');
        formData.append('matric', matric);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            verifyBtn.innerText = 'Verify';
            verifyBtn.disabled = false;

            if (data.success) {
                nameSpan.innerText = data.data.name;
                nameDisplay.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerText = 'Send Funds';
                confirmMsg.style.display = 'block';
                matricInput.readOnly = true; // Prevents changing after verify
            } else {
                alert(data.data.message || 'Verification failed.');
                nameDisplay.style.display = 'none';
                submitBtn.disabled = true;
                submitBtn.innerText = 'Verify Receiver First';
                confirmMsg.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            verifyBtn.innerText = 'Verify';
            verifyBtn.disabled = false;
        });
    });

    // Reset if matric changes (if we didn't make it readonly)
    matricInput.addEventListener('input', function() {
        nameDisplay.style.display = 'none';
        submitBtn.disabled = true;
        submitBtn.innerText = 'Verify Receiver First';
        confirmMsg.style.display = 'none';
    });
});
</script>
