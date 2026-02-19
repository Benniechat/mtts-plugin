<div class="wrap">
    <h1 class="wp-heading-inline">Form Builder</h1>
    <a href="?page=mtts-form-builder&action=new" class="page-title-action">Add New</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped mt-4">
        <thead>
            <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Shortcode</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $forms ) ) : ?>
                <tr>
                    <td colspan="4">No forms found. <a href="?page=mtts-form-builder&action=new">Create one</a>.</td>
                </tr>
            <?php else : ?>
                <?php foreach ( $forms as $form ) : ?>
                    <tr>
                        <td>
                            <strong><a href="?page=mtts-form-builder&action=edit&id=<?php echo $form->id; ?>"><?php echo esc_html( $form->title ); ?></a></strong>
                        </td>
                        <td><code><?php echo esc_html( $form->form_slug ); ?></code></td>
                        <td><code>[mtts_form slug="<?php echo esc_attr( $form->form_slug ); ?>"]</code></td>
                        <td><?php echo date( 'Y-m-d H:i', strtotime( $form->created_at ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
