<table>
    <tr>
        <th>Data</th>
        <th>Value</th>
    </tr>
    <tr>
        <td>team_unique_id</td>
        <td><input type="text" name='team_unique_id' value="<?php echo get_post_meta(($post->ID),'team_unique_id',true)?>"/></td>
    </tr>
    <tr>
        <td>Position</td>
        <td><input type="text" name='position' value="<?php echo get_post_meta(($post->ID),'position',true)?>"/></td>
    </tr>
    <tr>
        <td>Input order</td>
        <td><input type="text" name='input_order' value="<?php echo get_post_meta(($post->ID),'input_order',true)?>"/></td>
    </tr>
</table>