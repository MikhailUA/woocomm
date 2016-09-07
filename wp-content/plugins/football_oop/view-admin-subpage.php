<div>
    <h3>Upload form</h3>
<!--    <form action="<?php /*echo esc_url( admin_url('admin-post.php') ); */?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="import"/>
        <input id="imported_file" type="file" name="file"/>
        <input class="button" type="submit" name="btn_submit" value="Upload File"/>
    </form>-->

    <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post" enctype="multipart/form-data">
        <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Choose File">
        <input type="text" name="image_url" id="image_url" class="regular-text">
        <a href="#" id="clear">Clear Selection</a>
        </br>
        <input class="button" type="submit" name="btn_submit" value="Submit"/>


        <input type="hidden" name="action" value="import"/>
        <input type="hidden" name="file-id"  id="file-id" value=""/>
    </form>
</div>

<div>
    <h3>Delete all players</h3><a class="button" href="<?php echo add_query_arg('delete','all');?>">Delete</a>
</div>
