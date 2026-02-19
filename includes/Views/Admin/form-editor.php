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
                <div class="mtts-card" style="background:#fff; padding:15px; border:1px solid #ddd; position: sticky; top: 50px;">
                    <h3>Add Fields</h3>
                    <div class="mtts-field-buttons" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <button type="button" class="button mtts-add-field" data-type="text">Text</button>
                        <button type="button" class="button mtts-add-field" data-type="email">Email</button>
                        <button type="button" class="button mtts-add-field" data-type="number">Number</button>
                        <button type="button" class="button mtts-add-field" data-type="date">Date</button>
                        <button type="button" class="button mtts-add-field" data-type="textarea">Textarea</button>
                        <button type="button" class="button mtts-add-field" data-type="select">Select</button>
                        <button type="button" class="button mtts-add-field" data-type="checkbox">Checkbox</button>
                        <button type="button" class="button mtts-add-field" data-type="radio">Radio</button>
                    </div>
                    
                    <hr>
                    <input type="hidden" name="form_data" id="mtts-form-data-input">
                    <button type="submit" name="mtts_save_form" class="button button-primary button-large" style="width:100%;">Save Form</button>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldsList = document.getElementById('mtts-fields-list');
    const dataInput  = document.getElementById('mtts-form-data-input');
    const addButton  = document.querySelectorAll('.mtts-add-field');
    const form       = document.getElementById('mtts-form-builder-form');

    let fields = <?php echo json_encode( $fields ); ?>;

    function renderFields() {
        fieldsList.innerHTML = '';
        fields.forEach((field, index) => {
            const fieldEl = document.createElement('div');
            fieldEl.className = 'mtts-field-item';
            fieldEl.innerHTML = `
                <div class="mtts-field-header">
                    <strong>${field.type.toUpperCase()} Component</strong>
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
                    ${(field.type === 'select' || field.type === 'radio' || field.type === 'checkbox') ? `
                        <div class="full">
                            <label>Options (comma separated)</label><br>
                            <input type="text" class="widefat" value="${field.options || ''}" onchange="updateField(${index}, 'options', this.value)">
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
        dataInput.value = JSON.stringify({ fields: fields });
    }

    addButton.forEach(btn => {
        btn.addEventListener('click', () => {
            fields.push({
                type: btn.dataset.type,
                label: 'New ' + btn.dataset.type,
                placeholder: '',
                required: false,
                options: btn.dataset.type === 'select' ? 'Option 1, Option 2' : ''
            });
            renderFields();
        });
    });

    renderFields();
});
</script>
