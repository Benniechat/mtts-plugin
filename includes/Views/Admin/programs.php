<div class="wrap">
    <h1 class="wp-heading-inline">Programs</h1>
    <a href="#" class="page-title-action" onclick="document.getElementById('add-program-form').style.display='block'; return false;">Add New</a>
    <hr class="wp-header-end">

    <div id="mtts-form-container" style="display: none; margin-bottom: 20px; border: 1px solid #ccc; padding: 15px; background: #fff;">
        <h2 id="form-title">Add New Program</h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" id="program-form">
            <input type="hidden" name="action" value="mtts_save_program">
            <input type="hidden" name="id" id="program-id" value="0">
            <?php wp_nonce_field( 'mtts_save_program' ); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="name">Program Name</label></th>
                    <td><input type="text" name="name" id="name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="code">Program Code</label></th>
                    <td><input type="text" name="code" id="code" class="regular-text" required placeholder="e.g., BTH"></td>
                </tr>
                <tr>
                    <th><label for="duration_years">Duration (Years)</label></th>
                    <td><input type="number" name="duration_years" id="duration_years" min="1" max="10" value="4" required></td>
                </tr>
                <tr>
                    <th><label for="levels">Number of Levels</label></th>
                    <td><input type="number" name="levels" id="levels" min="1" max="10" value="4" required></td>
                </tr>
                <tr>
                    <th><label for="certificate_type">Certificate Type</label></th>
                    <td>
                        <select name="certificate_type" id="certificate_type">
                            <option value="Certificate">Certificate</option>
                            <option value="Diploma">Diploma</option>
                            <option value="Degree">Degree</option>
                            <option value="Masters">Masters</option>
                            <option value="PhD">PhD</option>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" id="submit-btn" class="button button-primary" value="Save Program">
                <button type="button" class="button" onclick="resetProgramForm()">Cancel</button>
            </p>
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Duration</th>
                <th>Levels</th>
                <th>Certificate</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $programs ) ) : ?>
                <?php foreach ( $programs as $program ) : ?>
                    <tr>
                        <td><?php echo esc_html( $program->name ); ?></td>
                        <td><?php echo esc_html( $program->code ); ?></td>
                        <td><?php echo esc_html( $program->duration_years ); ?> Years</td>
                        <td><?php echo esc_html( $program->levels ); ?></td>
                        <td><?php echo esc_html( $program->certificate_type ); ?></td>
                        <td>
                            <a href="#" class="button button-small" onclick="editProgram(<?php echo htmlspecialchars(json_encode($program)); ?>); return false;">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6">No programs found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function editProgram(program) {
    document.getElementById('mtts-form-container').style.display = 'block';
    document.getElementById('form-title').innerText = 'Edit Program: ' + program.name;
    document.getElementById('program-id').value = program.id;
    document.getElementById('name').value = program.name;
    document.getElementById('code').value = program.code;
    document.getElementById('duration_years').value = program.duration_years;
    document.getElementById('levels').value = program.levels;
    document.getElementById('certificate_type').value = program.certificate_type;
    document.getElementById('submit-btn').value = 'Update Program';
    window.scrollTo(0, 0);
}

function resetProgramForm() {
    document.getElementById('program-form').reset();
    document.getElementById('program-id').value = '0';
    document.getElementById('form-title').innerText = 'Add New Program';
    document.getElementById('submit-btn').value = 'Save Program';
    document.getElementById('mtts-form-container').style.display = 'none';
}
</script>
