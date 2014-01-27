<div class="wrap">
    <h2>
        Keywords for campaign: <?php echo $campaign->name; ?>
        <a href="<?php echo admin_url(); ?>admin.php?page=inbound_links_keywords&amp;action=add" class="add-new-h2">Add New Keyword</a>
    </h2>

    <?php include(__DIR__ . '/../elements/flash_messages.php'); ?>

    <p>To add keywords to this campaign simply use the URL: <strong><?php echo site_url() . '/?utm_campaign=' . $campaign->slug . '&utm_term=keyword_name'; ?></strong></p>

    <table class="wp-list-table widefat fixed buttons" cellspacing="0">
        <thead>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </th>
                <th scope="col" id="campaign-name">Keyword</th>
                <th scope="col" id="date" style="width:88px;">Created</th>
                <th scope="col" id="last-visit" style="width:88px;">Last Visit</th>
                <th scope="col" id="conversion" style="width:80px;text-align: center;">Conversion</th>
                <th scope="col" id="clicks" style="width:70px;text-align: center;">Clicks</th>
                <th scope="col" id="ctr" style="width:50px;text-align: center;">Trend</th>
                <th scope="col" id="actions" style="width:240px">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($data) > 0 ){ foreach ($data as $row) {  ?>

            <tr>
                <td>
                    <label class="screen-reader-text" for="cb-select-93">Select Campaign</label>
                    <input id="cb-select-<?php echo $row->id; ?>"" type="checkbox" name="post[]" value="<?php echo $row->id; ?>"">
                </td>
                <td><?php echo $row->name; ?></td>
                <td><?php echo date('Y/m/d', strtotime($row->created_at)); ?></td>
                <td><?php echo ( ( $row->updated_at === '0000-00-00 00:00:00' ) ? 'Never' : date('Y/m/d', strtotime($row->updated_at)) ); ?></td>
                <td style="text-align: center;">0%</td>
                <td style="text-align: center;"><?php echo $row->clicks; ?></td>
                <td style="text-align: center;">
                    <?php
                    if ($row->trend > 0)
                    {
                        echo '<span class="positive-trend">+' . $row->trend . '%</span>';
                    }elseif($row->trend < 0){
                        echo '<span class="negative-trend">' . $row->trend . '%</span>';
                    }else{
                        echo '<span class="no-trend">' . $row->trend . '%</span>';
                    }
                    ?>
                </td>
                <td>
                    Edit | Star | Trash
                </td>
            </tr>

        <?php } }else{ ?>
            <tr>
                <td colspan="6">No keywords in database.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
