<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormShortcode {

    public static function init() {
        add_shortcode( 'mtts_form', array( __CLASS__, 'render_form' ) );
    }

    public static function render_form( $atts ) {
        $atts = shortcode_atts( array(
            'slug' => '',
        ), $atts );

        if ( empty( $atts['slug'] ) ) {
            return '<p>Form slug is missing.</p>';
        }

        $form = \MttsLms\Models\Form::find_by_slug( $atts['slug'] );
        if ( ! $form ) {
            return '<p>Form not found.</p>';
        }

        // Check Deadline
        if ( ! empty( $form->submission_deadline ) ) {
            $deadline = strtotime( $form->submission_deadline );
            if ( time() > $deadline ) {
                return '<div class="mtts-alert mtts-alert-warning">
                    <strong>Submission Closed:</strong> The deadline for this application form (' . date( 'M j, Y H:i', $deadline ) . ') has passed.
                </div>';
            }
        }

        if ( isset( $_POST['mtts_dynamic_form_id'] ) ) {
            self::handle_submission( $form );
        }

        // --- PAYMENT GATEKEEPING ---
        if ( ! session_id() ) session_start();
        $paid_category = $_SESSION['mtts_form_paid_' . $form->id] ?? null;

        if ( ! $paid_category ) {
            return self::render_payment_selector( $form );
        }

        $form_data     = json_decode( $form->form_data, true );
        $fields        = isset( $form_data['fields'] ) ? $form_data['fields'] : array();
        
        // Filter Program of Choice based on Paid Category
        foreach ( $fields as &$field ) {
            if ( 'Program of Choice' === $field['label'] ) {
                $options = explode( ',', $field['options'] );
                if ( 'undergraduate' === $paid_category ) {
                    $filtered = array_filter( $options, function($opt) {
                        return in_array( trim($opt), ['Certificate', 'Diploma', 'Bachelor'] );
                    });
                } else {
                    $filtered = array_filter( $options, function($opt) {
                        return in_array( trim($opt), ['Masters of Divinity', 'PhD'] );
                    });
                }
                $field['options'] = implode( ', ', $filtered );
            }
        }

        $type          = isset( $form_data['type'] ) ? $form_data['type'] : 'standard';
        $save_continue = ! empty( $form_data['save_continue'] );

        // Check for existing session
        $session_key = isset( $_GET['mtts_resume'] ) ? sanitize_key( $_GET['mtts_resume'] ) : '';
        $session_data = array();
        if ( $session_key ) {
            $session = \MttsLms\Models\FormSession::get_session( $session_key );
            if ( $session ) {
                $session_data = json_decode( $session->partial_data, true );
            }
        }

        ob_start();
        ?>
        <div class="mtts-frontend-form <?php echo 'multistep' === $type ? 'mtts-multistep-form' : ''; ?>" id="mtts-form-<?php echo $form->id; ?>">
            <?php if ( 'multistep' === $type ) : ?>
                <div class="mtts-progress-container" style="margin-bottom: 30px;">
                    <div class="mtts-progress-bar" style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <div class="mtts-progress-fill" style="width: 0%; height: 100%; background: #7c3aed; transition: width 0.3s ease;"></div>
                    </div>
                    <div class="mtts-progress-steps" style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.85rem; color: #64748b;">
                        <?php 
                        $step_count = 1;
                        foreach( $fields as $f ) if ( 'section' === $f['type'] ) $step_count++;
                        for ( $i = 1; $i <= $step_count; $i++ ) : ?>
                            <span class="mtts-step-indicator" data-step-num="<?php echo $i; ?>">Step <?php echo $i; ?></span>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>

            <h3 class="mtts-form-title"><?php echo esc_html( $form->title ); ?></h3>
            <form method="post" action="" class="mtts-dynamic-form" data-form-id="<?php echo $form->id; ?>" enctype="multipart/form-data">
                <?php wp_nonce_field( 'mtts_submit_dynamic_form_' . $form->id ); ?>
                <input type="hidden" name="mtts_dynamic_form_id" value="<?php echo $form->id; ?>">
                <input type="hidden" name="mtts_session_key" value="<?php echo esc_attr( $session_key ); ?>">

                <?php 
                $step_index = 0;
                if ( 'multistep' === $type ) echo '<div class="mtts-form-step active" data-step="0">';

                foreach ( $fields as $index => $field ) : 
                    $field_id = "field_{$index}";
                    $required = ! empty( $field['required'] ) ? 'required' : '';
                    $value    = isset( $session_data[$field_id] ) ? $session_data[$field_id] : '';

                    if ( 'multistep' === $type && 'section' === $field['type'] && $index > 0 ) :
                        $step_index++;
                        echo '</div><div class="mtts-form-step" data-step="' . $step_index . '">';
                    endif;
                ?>
                    <div class="mtts-form-group" style="margin-bottom: 20px;">
                        <?php if ( 'section' !== $field['type'] && 'html' !== $field['type'] && 'hidden' !== $field['type'] ) : ?>
                            <label for="<?php echo $field_id; ?>">
                                <strong><?php echo esc_html( $field['label'] ); ?></strong>
                                <?php if ( $required ) : ?><span style="color:red;">*</span><?php endif; ?>
                            </label>
                            <br>
                        <?php endif; ?>

                        <?php if ( 'textarea' === $field['type'] ) : ?>
                            <textarea name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="mtts-form-control" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" <?php echo $required; ?>><?php echo esc_textarea($value); ?></textarea>
                        
                        <?php elseif ( 'select' === $field['type'] ) : ?>
                            <select name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="mtts-form-control" <?php echo $required; ?>>
                                <option value="">-- Select --</option>
                                <?php 
                                $options = explode( ',', $field['options'] );
                                foreach ( $options as $opt ) : $opt = trim($opt);
                                ?>
                                    <option value="<?php echo esc_attr( $opt ); ?>" <?php selected($value, $opt); ?>><?php echo esc_html( $opt ); ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php elseif ( 'radio' === $field['type'] ) : ?>
                            <?php 
                            $options = explode( ',', $field['options'] );
                            foreach ( $options as $opt ) : $opt = trim($opt);
                            ?>
                                <label style="display:block; margin-bottom:5px;"><input type="radio" name="<?php echo $field_id; ?>" value="<?php echo esc_attr( $opt ); ?>" <?php echo $required; ?> <?php checked($value, $opt); ?>> <?php echo esc_html( $opt ); ?></label>
                            <?php endforeach; ?>

                        <?php elseif ( 'checkbox' === $field['type'] ) : ?>
                            <?php 
                            $options = explode( ',', $field['options'] );
                            $selected_opts = is_array($value) ? $value : array();
                            foreach ( $options as $opt ) : $opt = trim($opt);
                            ?>
                                <label style="display:block; margin-bottom:5px;"><input type="checkbox" name="<?php echo $field_id; ?>[]" value="<?php echo esc_attr( $opt ); ?>" <?php checked(in_array($opt, $selected_opts)); ?>> <?php echo esc_html( $opt ); ?></label>
                            <?php endforeach; ?>

                        <?php elseif ( 'rating' === $field['type'] ) : ?>
                            <div class="mtts-rating-stars" data-field="<?php echo $field_id; ?>">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <span class="dashicons <?php echo $value >= $i ? 'dashicons-star-filled' : 'dashicons-star-empty'; ?>" data-value="<?php echo $i; ?>" style="font-size:30px; cursor:pointer; color:#fbbf24;"></span>
                                <?php endfor; ?>
                                <input type="hidden" name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" value="<?php echo esc_attr($value ?: 0); ?>" <?php echo $required; ?>>
                            </div>

                        <?php elseif ( 'signature' === $field['type'] ) : ?>
                            <div class="mtts-signature-pad" style="border:1px solid #ddd; background:#f9f9f9; border-radius:5px;">
                                <canvas id="sig-canvas-<?php echo $index; ?>" width="500" height="160" style="width:100%; height:160px; cursor:crosshair;"></canvas>
                                <div style="padding:5px; text-align:right;">
                                    <button type="button" class="mtts-sig-clear" data-canvas="sig-canvas-<?php echo $index; ?>">Clear</button>
                                </div>
                                <input type="hidden" name="<?php echo $field_id; ?>" id="sig-data-<?php echo $index; ?>" value="<?php echo esc_attr($value); ?>">
                            </div>

                        <?php elseif ( 'html' === $field['type'] ) : ?>
                            <div class="mtts-html-content"><?php echo $field['content']; ?></div>

                        <?php elseif ( 'section' === $field['type'] ) : ?>
                            <hr style="margin: 30px 0 20px 0; border:0; border-top: 2px solid #7c3aed;">
                            <h4 style="color:#7c3aed; margin-bottom:15px;"><?php echo esc_html( $field['label'] ); ?></h4>

                        <?php elseif ( 'paystack' === $field['type'] || 'flutterwave' === $field['type'] || 'stripe' === $field['type'] ) : ?>
                            <div class="mtts-payment-field" style="padding:15px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px;">
                                <strong>Payment via <?php echo ucfirst($field['type']); ?></strong>
                                <p><?php echo esc_html($field['label']); ?></p>
                                <input type="hidden" name="mtts_payment_gateway" value="<?php echo $field['type']; ?>">
                            </div>

                        <?php elseif ( 'slider' === $field['type'] ) : ?>
                            <input type="range" name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="mtts-form-control" style="padding:0;" min="0" max="100" value="<?php echo esc_attr($value ?: 50); ?>">
                        
                        <?php elseif ( 'nps' === $field['type'] ) : ?>
                            <div class="mtts-nps-field" style="display:flex; justify-content:space-between; max-width:500px;">
                                <?php for($i=0; $i<=10; $i++): ?>
                                    <label style="text-align:center; flex:1;">
                                        <input type="radio" name="<?php echo $field_id; ?>" value="<?php echo $i; ?>" <?php echo $required; ?> <?php checked($value, $i); ?>><br><?php echo $i; ?>
                                    </label>
                                <?php endfor; ?>
                            </div>

                        <?php elseif ( 'likert' === $field['type'] ) : ?>
                            <table style="width:100%; border-collapse: collapse;">
                                <tr>
                                    <?php 
                                    $likert_opts = ['Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree'];
                                    foreach($likert_opts as $opt): ?>
                                        <td style="text-align:center; padding:10px; border:1px solid #eee;">
                                            <label><input type="radio" name="<?php echo $field_id; ?>" value="<?php echo $opt; ?>" <?php echo $required; ?> <?php checked($value, $opt); ?>><br><?php echo $opt; ?></label>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            </table>

                        <?php elseif ( 'repeater' === $field['type'] ) : ?>
                            <div class="mtts-repeater-container" data-field="<?php echo $field_id; ?>" data-template="<?php echo esc_attr($field['fields']); ?>">
                                <div class="mtts-repeater-entries">
                                    <?php 
                                    $entries = is_array($value) ? $value : (json_decode($value, true) ?: [[]]);
                                    foreach ( $entries as $e_idx => $entry ) : ?>
                                        <div class="mtts-repeater-row" style="border:1px solid #e2e8f0; padding:15px; border-radius:8px; margin-bottom:10px; background:#fff; position:relative;">
                                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                                                <?php 
                                                $subfields = explode( ',', $field['fields'] );
                                                foreach ( $subfields as $subf ) : 
                                                    preg_match('/(.*)\((.*)\)/', $subf, $matches);
                                                    $sub_label = trim($matches[1] ?? $subf);
                                                    $sub_type = trim($matches[2] ?? 'text');
                                                    $sub_name = sanitize_title($sub_label);
                                                    $sub_val = $entry[$sub_name] ?? '';
                                                ?>
                                                    <div>
                                                        <label><?php echo esc_html($sub_label); ?></label><br>
                                                        <input type="<?php echo esc_attr($sub_type); ?>" name="<?php echo $field_id; ?>[<?php echo $e_idx; ?>][<?php echo $sub_name; ?>]" class="mtts-form-control" value="<?php echo esc_attr($sub_val); ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button" class="mtts-remove-row" style="position:absolute; top:5px; right:5px; background:none; border:0; color:red; cursor:pointer;" onclick="this.parentElement.remove()">×</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="mtts-add-row button" style="margin-top:5px;">+ Add Entry</button>
                            </div>

                        <?php else : ?>
                            <input type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo $field_id; ?>" id="<?php echo $field['id'] ?? $field_id; ?>" class="mtts-form-control <?php echo !empty($field['condition']) ? 'mtts-conditional' : ''; ?>" <?php echo !empty($field['condition']) ? 'data-condition="'.esc_attr($field['condition']).'"' : ''; ?> placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" <?php echo $required; ?> <?php echo !empty($field['readonly']) ? 'readonly' : ''; ?> value="<?php echo esc_attr($value); ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if ( 'multistep' === $type ) echo '</div>'; // Close last step ?>

                <div class="mtts-form-footer" style="margin-top:30px; display:flex; gap:10px; flex-wrap:wrap;">
                    <?php if ( 'multistep' === $type ) : ?>
                        <button type="button" class="mtts-btn mtts-btn-prev" style="display:none; background:#94a3b8; color:#fff;">Previous</button>
                        <button type="button" class="mtts-btn mtts-btn-next mtts-btn-primary" style="background: #7c3aed; color: #fff; border: 0; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-weight: 600;">Next</button>
                    <?php endif; ?>

                    <button type="submit" class="mtts-btn mtts-btn-primary mtts-submit-btn" <?php echo 'multistep' === $type ? 'style="display:none;"' : ''; ?> style="background: #7c3aed; color: #fff; border: 0; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                        Submit Form
                    </button>

                    <?php if ( $save_continue ) : ?>
                        <button type="button" class="mtts-btn-save-continue" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; padding:10px 20px; border-radius:5px; cursor:pointer; font-weight: 600;">
                            Save & Continue Later
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <style>
            .mtts-form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-top: 5px; }
            .mtts-rating-stars .dashicons-star-filled { color: #fbbf24; }
            .mtts-form-step { display: none; }
            .mtts-form-step.active { display: block; animation: mttsFadeIn 0.3s ease; }
            @keyframes mttsFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .mtts-hidden { display: none; }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.getElementById('mtts-form-<?php echo $form->id; ?>');
            if (!formContainer) return;

            const steps = formContainer.querySelectorAll('.mtts-form-step');
            const progressFill = formContainer.querySelector('.mtts-progress-fill');
            const stepIndicators = formContainer.querySelectorAll('.mtts-step-indicator');
            const prevBtn = formContainer.querySelector('.mtts-btn-prev');
            const nextBtn = formContainer.querySelector('.mtts-btn-next');
            const submitBtn = formContainer.querySelector('.mtts-submit-btn');
            const saveBtn = formContainer.querySelector('.mtts-btn-save-continue');
            const form = formContainer.querySelector('form');
            let currentStep = 0;

            function updateSteps() {
                steps.forEach((step, idx) => {
                    step.classList.toggle('active', idx === currentStep);
                });

                if (progressFill) {
                    const percent = (currentStep / (steps.length - 1)) * 100;
                    progressFill.style.width = percent + '%';
                }

                stepIndicators.forEach((ind, idx) => {
                    if (idx <= currentStep) {
                        ind.style.color = '#7c3aed';
                        ind.style.fontWeight = 'bold';
                    } else {
                        ind.style.color = '#64748b';
                        ind.style.fontWeight = 'normal';
                    }
                });

                if (prevBtn) prevBtn.style.display = currentStep === 0 ? 'none' : 'inline-block';
                if (nextBtn) nextBtn.style.display = currentStep === steps.length - 1 ? 'none' : 'inline-block';
                if (submitBtn) submitBtn.style.display = (currentStep === steps.length - 1 || steps.length === 0) ? 'inline-block' : 'none';
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    const currentStepEl = steps[currentStep];
                    const inputs = currentStepEl.querySelectorAll('[required]');
                    let valid = true;
                    inputs.forEach(input => {
                        if (!input.value) {
                            valid = false;
                            input.style.borderColor = 'red';
                        } else {
                            input.style.borderColor = '#ddd';
                        }
                    });

                    if (valid) {
                        currentStep++;
                        updateSteps();
                    } else {
                        alert('Please fill all required fields in this step.');
                    }
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    if (currentStep > 0) {
                        currentStep--;
                        updateSteps();
                    }
                });
            }

            if (saveBtn) {
                saveBtn.addEventListener('click', function() {
                    const formData = new FormData(form);
                    formData.append('action', 'mtts_save_form_session');
                    formData.append('mtts_form_id', '<?php echo $form->id; ?>');
                    formData.append('last_step', currentStep);

                    this.innerHTML = 'Saving...';
                    this.disabled = true;

                    fetch(ajaxurl || '/wp-admin/admin-ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Progress saved! Link: ' + data.data.resume_url);
                        } else {
                            alert('Error: ' + data.data.message);
                        }
                    })
                    .finally(() => {
                        this.innerHTML = 'Save & Continue Later';
                        this.disabled = false;
                    });
                });
            }

            // Rating Logic
            formContainer.querySelectorAll('.mtts-rating-stars').forEach(container => {
                const stars = container.querySelectorAll('.dashicons');
                const input = container.querySelector('input');
                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const val = this.dataset.value;
                        input.value = val;
                        stars.forEach(s => {
                            if (s.dataset.value <= val) {
                                s.classList.remove('dashicons-star-empty');
                                s.classList.add('dashicons-star-filled');
                            } else {
                                s.classList.remove('dashicons-star-filled');
                                s.classList.add('dashicons-star-empty');
                            }
                        });
                    });
                });
            });

            // Signature Logic
            formContainer.querySelectorAll('.mtts-signature-pad').forEach(pad => {
                const canvas = pad.querySelector('canvas');
                const ctx = canvas.getContext('2d');
                const clearBtn = pad.querySelector('.mtts-sig-clear');
                const input = pad.querySelector('input');
                let drawing = false;

                canvas.addEventListener('mousedown', () => drawing = true);
                canvas.addEventListener('mouseup', () => {
                    drawing = false;
                    ctx.beginPath();
                    input.value = canvas.toDataURL();
                });
                canvas.addEventListener('mousemove', (e) => {
                    if (!drawing) return;
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.strokeStyle = '#000';
                    const rect = canvas.getBoundingClientRect();
                    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
                });
                clearBtn.addEventListener('click', () => { ctx.clearRect(0, 0, canvas.width, canvas.height); input.value = ''; });
            });

            // Repeater Logic
            formContainer.querySelectorAll('.mtts-repeater-container').forEach(container => {
                const addBtn = container.querySelector('.mtts-add-row');
                const entries = container.querySelector('.mtts-repeater-entries');
                const fieldId = container.dataset.field;
                const template = container.dataset.template;

                addBtn.addEventListener('click', () => {
                    const idx = entries.children.length;
                    const row = document.createElement('div');
                    row.className = 'mtts-repeater-row';
                    row.style = 'border:1px solid #e2e8f0; padding:15px; border-radius:8px; margin-bottom:10px; background:#fff; position:relative;';
                    
                    let fieldsHtml = '<div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">';
                    template.split(',').forEach(subf => {
                        const matches = subf.match(/(.*)\((.*)\)/);
                        const label = (matches ? matches[1] : subf).trim();
                        const type = (matches ? matches[2] : 'text').trim();
                        const name = label.toLowerCase().replace(/ /g, '_').replace(/[^\w-]+/g, '');
                        fieldsHtml += `<div><label>${label}</label><br><input type="${type}" name="${fieldId}[${idx}][${name}]" class="mtts-form-control"></div>`;
                    });
                    fieldsHtml += '</div><button type="button" class="mtts-remove-row" style="position:absolute; top:5px; right:5px; background:none; border:0; color:red; cursor:pointer;" onclick="this.parentElement.remove()">×</button>';
                    
                    row.innerHTML = fieldsHtml;
                    entries.appendChild(row);
                });
            });

            // Conditional Logic & Field Calculation
            function checkConditions() {
                formContainer.querySelectorAll('[data-condition]').forEach(el => {
                    const condition = el.closest('.mtts-form-group').querySelector('input, select, textarea').getAttribute('data-condition') || el.dataset.condition;
                    if (!condition) return;

                    const [targetId, targetVal] = condition.includes('==') ? condition.split('==') : [null, null];
                    if (!targetId) return;

                    const targetEl = document.getElementById(targetId);
                    if (!targetEl) return;

                    let currentVal = targetEl.value;
                    if (targetEl.type === 'radio' || targetEl.type === 'checkbox') {
                        const checked = form.querySelector(`input[name="${targetEl.name}"]:checked`);
                        currentVal = checked ? checked.value : '';
                    }

                    const show = currentVal === targetVal;
                    el.closest('.mtts-form-group').style.display = show ? 'block' : 'none';
                    if (!show) {
                        const input = el.closest('.mtts-form-group').querySelector('input, select, textarea');
                        if (input) input.required = false;
                    } else {
                        // Restore required if it was originally required
                        // For simplicity, we'll just check if it has the required attribute in HTML
                    }
                });
            }

            form.addEventListener('change', checkConditions);
            checkConditions();

            // Age Calculation
            const dobInput = document.getElementById('dob');
            const ageInput = document.getElementById('age_field');
            if (dobInput && ageInput) {
                dobInput.addEventListener('change', () => {
                    const birthDate = new Date(dobInput.value);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    ageInput.value = age > 0 ? age : 0;
                });
            }

            // Current Date Auto-gen
            const dateField = document.getElementById('current_date');
            if (dateField) dateField.value = new Date().toISOString().split('T')[0];
        });
        </script>
        <?php
        return ob_get_clean();
    }

    private static function handle_submission( $form ) {
        if ( ! \MttsLms\Core\Security::check_request( 'mtts_submit_dynamic_form_' . $form->id ) ) return;

        $fields_raw = json_decode( $form->form_data, true );
        $fields     = isset( $fields_raw['fields'] ) ? $fields_raw['fields'] : array();
        
        $post_data  = \MttsLms\Core\Security::sanitize_deep( $_POST );
        $entry_data = array();
        $applicant_email = '';
        $full_name = '';

        foreach ( $fields as $index => $field ) {
            $field_id = "field_{$index}";
            $label    = $field['label'];

            if ( 'file' === $field['type'] && ! empty( $_FILES[$field_id]['name'] ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                $uploaded = wp_handle_upload( $_FILES[$field_id], array( 'test_form' => false ) );
                if ( isset( $uploaded['url'] ) ) {
                    $entry_data[$label] = $uploaded['url'];
                }
            } elseif ( isset( $post_data[$field_id] ) ) {
                $entry_data[$label] = $post_data[$field_id];
                
                // Track applicant email
                if ( 'email' === $field['type'] && (stripos($label, 'applicant') !== false || stripos($label, 'email') !== false) && empty($applicant_email) ) {
                    $applicant_email = sanitize_email($post_data[$field_id]);
                }

                // Collect name for user profile
                if ( stripos($label, 'surname') !== false ) {
                    $full_name = trim($post_data[$field_id] . ' ' . $full_name);
                } elseif ( stripos($label, 'forename') !== false || stripos($label, 'first') !== false ) {
                    $full_name = trim($full_name . ' ' . $post_data[$field_id]);
                }
            }
        }

        if ( empty($applicant_email) ) {
            echo '<div class="mtts-alert mtts-alert-warning">' . esc_html__('Missing email address.', 'mtts-lms') . '</div>';
            return;
        }

        // Check for existing user
        if ( email_exists($applicant_email) ) {
            echo '<div class="mtts-alert mtts-alert-warning">' . esc_html__('An account with this email already exists.', 'mtts-lms') . '</div>';
            return;
        }

        // 1. Create User Account (Pending)
        $username = $applicant_email; // Temporary username, will be replaced by Matric Number on approval
        $password = wp_generate_password();
        $user_id = wp_create_user( $username, $password, $applicant_email );

        if ( is_wp_error($user_id) ) {
            echo '<div class="mtts-alert mtts-alert-warning">' . esc_html($user_id->get_error_message()) . '</div>';
            return;
        }

        // 2. Assign Role and Meta
        wp_update_user( array(
            'ID'           => $user_id,
            'display_name' => $full_name,
            'role'         => 'mtts_student'
        ) );
        update_user_meta( $user_id, 'mtts_account_status', 'pending' );

        // 3. Save Entry
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'mtts_form_entries',
            array(
                'form_id'    => $form->id,
                'user_id'    => $user_id,
                'entry_data' => json_encode( $entry_data ),
                'status'     => 'pending',
                'created_at' => current_time( 'mysql' )
            )
        );

        // Send Notifications (Initial Submission)
        self::send_notifications( $form, $entry_data, $applicant_email );

        // Clear session if exists
        if ( ! empty( $_POST['mtts_session_key'] ) ) {
            \MttsLms\Models\FormSession::delete_session( sanitize_key( $_POST['mtts_session_key'] ) );
        }

        echo '<div class="mtts-alert mtts-alert-success" style="padding:15px; background:#dcfce7; border-left:4px solid #22c55e; margin-bottom:20px;">' .
             esc_html__( 'Application submitted successfully! A pending student account has been created. You will receive an email once your application is approved.', 'mtts-lms' ) . 
             '</div>';
    }

    private static function send_notifications( $form, $entry_data, $applicant_email ) {
        $admin_email = get_option( 'admin_email' );
        $subject_admin = "New Form Submission: " . $form->title;
        $subject_applicant = "Confirmation: " . $form->title;

        $body = "<h2>Submission Details</h2><table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        foreach ( $entry_data as $label => $value ) {
            if ( is_array($value) ) {
                $value = json_encode($value);
            }
            $body .= "<tr><td><strong>{$label}</strong></td><td>" . esc_html($value) . "</td></tr>";
        }
        $body .= "</table>";

        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Admin Notification
        wp_mail( $admin_email, $subject_admin, $body, $headers );

        // Applicant Notification
        if ( ! empty( $applicant_email ) ) {
            $applicant_body = "<p>Dear Applicant,</p><p>Thank you for submitting your application to Mountain Top Theological Seminary.</p>" . 
                               "<p>Your submission for <strong>" . esc_html($form->title) . "</strong> has been received and is currently under review.</p>" . 
                               "<p>Best regards,<br>MTTS Admissions Team</p>";
            wp_mail( $applicant_email, $subject_applicant, $applicant_body, $headers );
        }
    }

    private static function render_payment_selector( $form ) {
        $ug_price = get_option('mtts_undergraduate_form_price', '5000');
        $pg_price = get_option('mtts_postgraduate_form_price', '10000');
        $pk       = get_option('mtts_paystack_public_key');

        ob_start();
        ?>
        <div class="mtts-payment-gatekeeper">
            <div class="mtts-card payment-selection-card">
                <div class="payment-header">
                    <span class="dashicons dashicons-cart"></span>
                    <h3>Purchase Admission Form</h3>
                    <p>Select your intended program category to proceed to the application form.</p>
                </div>

                <div class="category-options">
                    <div class="category-box" onclick="selectFormCategory('undergraduate', <?php echo $ug_price; ?>)">
                        <div class="category-icon"><span class="dashicons dashicons-welcome-learn-more"></span></div>
                        <h4>Undergraduate</h4>
                        <p>Certificate, Diploma, Bachelor of Theology</p>
                        <div class="price">₦<?php echo number_format($ug_price); ?></div>
                    </div>

                    <div class="category-box" onclick="selectFormCategory('postgraduate', <?php echo $pg_price; ?>)">
                        <div class="category-icon"><span class="dashicons dashicons-awards"></span></div>
                        <h4>Postgraduate</h4>
                        <p>Masters of Divinity, PhD</p>
                        <div class="price">₦<?php echo number_format($pg_price); ?></div>
                    </div>
                </div>

                <div id="payment-actions" style="display:none; margin-top:30px; text-align:center;">
                    <p id="selection-summary" style="margin-bottom:15px; font-weight:600; color:#475569;"></p>
                    <button type="button" id="pay-btn" class="mtts-btn mtts-btn-primary" style="width:100%;" onclick="initiateFormPayment()">
                        Pay ₦<span id="display-price">0</span> & Start Application
                    </button>
                    <p class="text-muted" style="font-size:12px; margin-top:10px;">Secure payment powered by Paystack</p>
                </div>
            </div>
        </div>

        <script src="https://js.paystack.co/v1/inline.js"></script>
        <script>
        let selectedCategory = '';
        let selectedPrice = 0;

        function selectFormCategory(cat, price) {
            selectedCategory = cat;
            selectedPrice = price;
            
            document.querySelectorAll('.category-box').forEach(box => box.classList.remove('selected'));
            event.currentTarget.classList.add('selected');

            document.getElementById('payment-actions').style.display = 'block';
            document.getElementById('selection-summary').innerText = 'Selected: ' + cat.charAt(0).toUpperCase() + cat.slice(1) + ' Admission Form';
            document.getElementById('display-price').innerText = price.toLocaleString();
            
            document.getElementById('payment-actions').scrollIntoView({ behavior: 'smooth', block: 'end' });
        }

        function initiateFormPayment() {
            if (!selectedCategory) return;

            const handler = PaystackPop.setup({
                key: '<?php echo esc_js($pk); ?>',
                email: 'applicant-' + Date.now() + '@mttseminary.org', // Temporary placeholder email
                amount: selectedPrice * 100,
                currency: 'NGN',
                callback: function(response) {
                    verifyPayment(response.reference);
                },
                onClose: function() {
                    alert('Payment cancelled. You must purchase the form to proceed.');
                }
            });
            handler.openIframe();
        }

        function verifyPayment(ref) {
            const btn = document.getElementById('pay-btn');
            btn.innerHTML = '<span class="dashicons dashicons-update spin"></span> Verifying...';
            btn.disabled = true;

            jQuery.post(ajaxurl || '/wp-admin/admin-ajax.php', {
                action: 'mtts_verify_form_payment',
                reference: ref,
                category: selectedCategory,
                form_id: <?php echo $form->id; ?>
            }, function(res) {
                if (res.success) {
                    window.location.reload();
                } else {
                    alert('Verification failed: ' + res.data.message);
                    btn.innerHTML = 'Pay & Start Application';
                    btn.disabled = false;
                }
            });
        }
        </script>

        <style>
        .mtts-payment-gatekeeper { max-width: 800px; margin: 50px auto; font-family: 'Inter', sans-serif; }
        .payment-selection-card { padding: 40px; text-align: center; border-top: 6px solid #7c3aed; }
        .payment-header h3 { font-size: 24px; color: #1e293b; margin: 15px 0 10px; font-weight: 800; }
        .payment-header .dashicons { font-size: 48px; width: 48px; height: 48px; color: #7c3aed; }
        .category-options { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
        .category-box { border: 2px solid #e2e8f0; padding: 25px; border-radius: 15px; cursor: pointer; transition: all 0.3s ease; }
        .category-box:hover { border-color: #7c3aed; transform: translateY(-5px); box-shadow: 0 10px 20px rgba(124, 58, 237, 0.1); }
        .category-box.selected { border-color: #7c3aed; background: #f5f3ff; }
        .category-icon .dashicons { font-size: 40px; width: 40px; height: 40px; color: #7c3aed; }
        .category-box h4 { margin: 15px 0 10px; font-size: 18px; color: #1e293b; }
        .category-box .price { font-size: 22px; font-weight: 800; color: #7c3aed; margin-top: 15px; }
        .spin { animation: mtts-spin 1s linear infinite; }
        @keyframes mtts-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @media (max-width: 600px) { .category-options { grid-template-columns: 1fr; } }
        </style>
        <?php
        return ob_get_clean();
    }
}
