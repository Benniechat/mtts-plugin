<?php
/**
 * Form Editor View
 */
$form_data = $form ? json_decode( $form->form_data, true ) : array();
$fields    = isset( $form_data['fields'] ) ? $form_data['fields'] : array();
?>
<div class="wrap">
    <h1><?php echo $form ? 'Edit Form' : 'Create New Form'; ?></h1>
    
    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Form saved successfully.</p></div>
    <?php endif; ?>

    <form method="post" action="" id="mtts-form-builder-form">
        <?php wp_nonce_field( 'mtts_save_form' ); ?>
        
        <div style="display:grid; grid-template-columns: 1fr 300px; gap:30px; margin-top:20px;">
            
            <div id="mtts-builder-main">
                <div class="mtts-card" style="background:#fff; padding:20px; border:1px solid #ddd; border-top: 4px solid #7c3aed;">
                    <div class="mtts-form-group">
                        <label><strong>Form Title</strong></label>
                        <input type="text" name="form_title" class="regular-text" style="width:100%; font-size:1.2rem;" value="<?php echo $form ? esc_attr( $form->title ) : ''; ?>" required placeholder="e.g. Student Feedback Form">
                    </div>
                    <div class="mtts-form-group" style="margin-top:10px;">
                        <label><strong>Form Slug</strong></label>
                        <input type="text" name="form_slug" class="regular-text" style="width:100%;" value="<?php echo $form ? esc_attr( $form->form_slug ) : ''; ?>" required placeholder="e.g. feedback-form">
                    </div>
                </div>

                <div id="mtts-fields-container" style="margin-top:30px;">
                    <h3>Form Fields</h3>
                    <div id="mtts-fields-list">
                        <!-- Fields will be injected here by JS -->
                    </div>
                    
                    <div style="text-align:center; padding:30px; border:2px dashed #ccc; border-radius:10px; background:#f9f9f9; margin-top:20px;">
                        <p style="color:#666;">Add fields from the right panel to build your form.</p>
                    </div>
                </div>
            </div>

            <div id="mtts-builder-sidebar">
                <div class="mtts-card" style="background:#f0f0f1; padding:15px; border:1px solid #ddd; border-radius:8px;">
                    <h3>AI & Templates</h3>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:20px;">
                        <button type="button" class="button button-secondary" id="mtts-ai-gen-btn" style="background:#7c3aed; color:#fff; border:0;"><span class="dashicons dashicons-art" style="margin-top:3px;"></span> AI Generator</button>
                        <button type="button" class="button button-secondary" id="mtts-templates-btn"><span class="dashicons dashicons-layout" style="margin-top:3px;"></span> Templates</button>
                    </div>

                    <h3>Form Settings</h3>
                    <div style="background:#fff; padding:10px; border-radius:5px; margin-bottom:20px; border:1px solid #ddd;">
                        <div style="margin-bottom:10px;">
                            <label style="display:block; margin-bottom:5px; font-weight:bold;">Form Type</label>
                            <select id="mtts-form-type" class="widefat">
                                <option value="standard" <?php echo (isset($form_data['type']) && $form_data['type'] == 'standard') ? 'selected' : ''; ?>>Standard Form</option>
                                <option value="multistep" <?php echo (isset($form_data['type']) && $form_data['type'] == 'multistep') ? 'selected' : ''; ?>>Multi-step Form</option>
                            </select>
                        </div>
                        <div style="margin-bottom:10px;">
                            <label style="display:block; margin-bottom:5px; font-weight:bold;">Submission Deadline</label>
                            <input type="datetime-local" name="submission_deadline" class="widefat" value="<?php echo !empty($form->submission_deadline) ? date('Y-m-d\TH:i', strtotime($form->submission_deadline)) : ''; ?>">
                        </div>
                        <div>
                            <label>
                                <input type="checkbox" id="mtts-save-continue" value="1" <?php echo !empty($form_data['save_continue']) ? 'checked' : ''; ?>>
                                Enable "Save & Continue"
                            </label>
                        </div>
                    </div>

                    <h3>Standard Fields</h3>
                    <div class="mtts-field-buttons" style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; margin-bottom:15px;">
                        <button type="button" class="button mtts-add-field" data-type="text">Single Line Text</button>
                        <button type="button" class="button mtts-add-field" data-type="textarea">Paragraph Text</button>
                        <button type="button" class="button mtts-add-field" data-type="select">Dropdown</button>
                        <button type="button" class="button mtts-add-field" data-type="radio">Multiple Choice</button>
                        <button type="button" class="button mtts-add-field" data-type="checkbox">Checkboxes</button>
                        <button type="button" class="button mtts-add-field" data-type="number">Numbers</button>
                        <button type="button" class="button mtts-add-field" data-type="name">Name</button>
                        <button type="button" class="button mtts-add-field" data-type="email">Email</button>
                        <button type="button" class="button mtts-add-field" data-type="slider">Number Slider</button>
                        <button type="button" class="button mtts-add-field" data-type="captcha">CAPTCHA</button>
                    </div>

                    <h3>Fancy Fields</h3>
                    <div class="mtts-field-buttons" style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; margin-bottom:15px;">
                        <button type="button" class="button mtts-add-field" data-type="tel">Phone</button>
                        <button type="button" class="button mtts-add-field" data-type="address">Address</button>
                        <button type="button" class="button mtts-add-field" data-type="datetime">Date / Time</button>
                        <button type="button" class="button mtts-add-field" data-type="url">Website / URL</button>
                        <button type="button" class="button mtts-add-field" data-type="password">Password</button>
                        <button type="button" class="button mtts-add-field" data-type="hidden">Hidden Field</button>
                        <button type="button" class="button mtts-add-field" data-type="file">File Upload</button>
                        <button type="button" class="button mtts-add-field" data-type="signature">Signature</button>
                        <button type="button" class="button mtts-add-field" data-type="rating">Rating</button>
                        <button type="button" class="button mtts-add-field" data-type="likert">Likert Scale</button>
                        <button type="button" class="button mtts-add-field" data-type="nps">Net Promoter Score</button>
                        <button type="button" class="button mtts-add-field" data-type="repeater">Repeater</button>
                        <button type="button" class="button mtts-add-field" data-type="section">Section Divider</button>
                        <button type="button" class="button mtts-add-field" data-type="html">HTML Block</button>
                    </div>

                    <h3>Payment Fields</h3>
                    <div class="mtts-field-buttons" style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; margin-bottom:15px;">
                        <button type="button" class="button mtts-add-field" data-type="paystack">Paystack</button>
                        <button type="button" class="button mtts-add-field" data-type="flutterwave">Flutterwave</button>
                        <button type="button" class="button mtts-add-field" data-type="stripe">Stripe</button>
                        <button type="button" class="button mtts-add-field" data-type="paypal">PayPal</button>
                        <button type="button" class="button mtts-add-field" data-type="total">Order Total</button>
                    </div>
                    
                    <hr>
                    <input type="hidden" name="form_data" id="mtts-form-data-input">
                    <button type="submit" name="mtts_save_form" class="button button-primary button-large" style="width:100%; height:50px; font-size:1.1rem;">Save Form</button>
                </div>
            </div>

            <!-- Modals -->
            <div id="mtts-ai-modal" class="mtts-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; align-items:center; justify-content:center;">
                <div class="mtts-modal-content" style="background:#fff; padding:30px; border-radius:12px; width:500px; max-width:90%;">
                    <h2>AI Form Generator</h2>
                    <p>Describe the form you want to create, and Gemini will build it for you.</p>
                    <textarea id="mtts-ai-prompt" style="width:100%; height:100px; margin-bottom:20px;" placeholder="e.g. A student feedback form with rating for courses, name, and comment field..."></textarea>
                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <button type="button" class="button" id="mtts-ai-cancel">Cancel</button>
                        <button type="button" class="button button-primary" id="mtts-ai-generate">Generate Form</button>
                    </div>
                </div>
            </div>

            <div id="mtts-template-modal" class="mtts-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; align-items:center; justify-content:center;">
                <div class="mtts-modal-content" style="background:#fff; padding:30px; border-radius:12px; width:500px; max-width:90%;">
                    <h2>Select a Template</h2>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:20px;" id="mtts-template-list">
                        <?php 
                        $templates = \MttsLms\Core\Forms\FormTemplates::get_templates();
                        foreach ( $templates as $id => $tpl ) : ?>
                            <div class="template-item" data-id="<?php echo $id; ?>" style="border:1px solid #ddd; padding:15px; border-radius:8px; cursor:pointer; text-align:center;">
                                <strong><?php echo esc_html( $tpl['title'] ); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="text-align:right;">
                        <button type="button" class="button" id="mtts-template-cancel">Cancel</button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<style>
.mtts-field-item {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    position: relative;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.mtts-field-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    cursor: move;
}
.mtts-field-type-badge {
    background: #e0e7ff;
    color: #4338ca;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}
.mtts-field-item .dashicons-dismiss {
    color: #cc0000;
    cursor: pointer;
}
.mtts-field-settings {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.mtts-field-settings .full {
    grid-column: span 2;
}
.template-item:hover {
    background: #f8fafc;
    border-color: #7c3aed !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldsList = document.getElementById('mtts-fields-list');
    const dataInput  = document.getElementById('mtts-form-data-input');
    const addButton  = document.querySelectorAll('.mtts-add-field');
    const form       = document.getElementById('mtts-form-builder-form');

    // Modals
    const aiModal    = document.getElementById('mtts-ai-modal');
    const tplModal   = document.getElementById('mtts-template-modal');
    const aiGenBtn   = document.getElementById('mtts-ai-gen-btn');
    const tplBtn     = document.getElementById('mtts-templates-btn');

    let fields = <?php echo json_encode( $fields ); ?>;

    function renderFields() {
        fieldsList.innerHTML = '';
        if (fields.length === 0) {
            fieldsList.innerHTML = '<div style="text-align:center; padding:30px; border:2px dashed #ccc; border-radius:10px; background:#f9f9f9; margin-top:20px;"><p style="color:#666;">Add fields from the right panel to build your form.</p></div>';
        }
        fields.forEach((field, index) => {
            const fieldEl = document.createElement('div');
            fieldEl.className = 'mtts-field-item';
            fieldEl.innerHTML = `
                <div class="mtts-field-header">
                    <span><span class="mtts-field-type-badge">${field.type.toUpperCase()}</span> <strong>${field.label}</strong></span>
                    <span class="dashicons dashicons-dismiss" onclick="removeField(${index})"></span>
                </div>
                <div class="mtts-field-settings">
                    <div class="full">
                        <label>Label</label><br>
                        <input type="text" class="widefat" value="${field.label}" onchange="updateField(${index}, 'label', this.value)">
                    </div>
                    <div>
                        <label>Placeholder</label><br>
                        <input type="text" class="widefat" value="${field.placeholder || ''}" onchange="updateField(${index}, 'placeholder', this.value)">
                    </div>
                    <div>
                        <label>Required?</label><br>
                        <select onchange="updateField(${index}, 'required', this.value === 'yes')">
                            <option value="no" ${!field.required ? 'selected' : ''}>No</option>
                            <option value="yes" ${field.required ? 'selected' : ''}>Yes</option>
                        </select>
                    </div>
                    ${['select', 'radio', 'checkbox', 'repeater'].includes(field.type) ? `
                        <div class="full">
                            <label>Options (comma separated)</label><br>
                            <input type="text" class="widefat" value="${field.options || ''}" onchange="updateField(${index}, 'options', this.value)">
                        </div>
                    ` : ''}
                    ${field.type === 'html' ? `
                         <div class="full">
                            <label>HTML Content</label><br>
                            <textarea class="widefat" onchange="updateField(${index}, 'content', this.value)">${field.content || ''}</textarea>
                        </div>
                    ` : ''}
                </div>
            `;
            fieldsList.appendChild(fieldEl);
        });
        updateDataInput();
    }

    window.removeField = function(index) {
        fields.splice(index, 1);
        renderFields();
    };

    window.updateField = function(index, key, value) {
        fields[index][key] = value;
        updateDataInput();
    };

    function updateDataInput() {
        const type = document.getElementById('mtts-form-type').value;
        const saveContinue = document.getElementById('mtts-save-continue').checked ? 1 : 0;
        dataInput.value = JSON.stringify({ 
            fields: fields,
            type: type,
            save_continue: saveContinue
        });
    }

    document.getElementById('mtts-form-type').addEventListener('change', updateDataInput);
    document.getElementById('mtts-save-continue').addEventListener('change', updateDataInput);

    addButton.forEach(btn => {
        btn.addEventListener('click', () => {
            fields.push({
                type: btn.dataset.type,
                label: 'New ' + btn.dataset.type,
                placeholder: '',
                required: false,
                options: ''
            });
            renderFields();
        });
    });

    // AI Modal Logic
    aiGenBtn.addEventListener('click', () => aiModal.style.display = 'flex');
    document.getElementById('mtts-ai-cancel').addEventListener('click', () => aiModal.style.display = 'none');
    document.getElementById('mtts-ai-generate').addEventListener('click', function() {
        const prompt = document.getElementById('mtts-ai-prompt').value;
        if (!prompt) return alert('Please enter a prompt.');
        
        this.innerHTML = 'Generating...';
        this.disabled = true;

        fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'mtts_ai_generate_form',
                prompt: prompt,
                nonce: '<?php echo wp_create_nonce("mtts_form_builder_nonce"); ?>'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fields = data.data.fields;
                renderFields();
                aiModal.style.display = 'none';
            } else {
                alert(data.data.message);
            }
        })
        .finally(() => {
            this.innerHTML = 'Generate Form';
            this.disabled = false;
        });
    });

    // Template Modal Logic
    tplBtn.addEventListener('click', () => tplModal.style.display = 'flex');
    document.getElementById('mtts-template-cancel').addEventListener('click', () => tplModal.style.display = 'none');
    document.querySelectorAll('.template-item').forEach(item => {
        item.addEventListener('click', function() {
            const tplId = this.dataset.id;
            fetch(ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'mtts_get_form_template',
                    template_id: tplId,
                    nonce: '<?php echo wp_create_nonce("mtts_form_builder_nonce"); ?>'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    fields = data.data.fields;
                    document.querySelector('input[name="form_title"]').value = data.data.title;
                    renderFields();
                    tplModal.style.display = 'none';
                }
            });
        });
    });

    renderFields();
});
</script>
