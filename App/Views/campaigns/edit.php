<div class="wrap">
    <h2>
        Edit Campaign
    </h2>

    <?php include(__DIR__ . '/../elements/flash_messages.php'); ?>

    <form method="post" action="<?php echo admin_url(); ?>admin.php?page=inbound_links_record&amp;action=update&amp;id=<?php echo $data->id; ?>">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="name">Name</label></th>
                    <td>
                        <input name="name" type="text" id="name"
                               value="<?php echo(isset($flashMessages['inputs']['name']) ? $flashMessages['inputs']['name'] : $data->name); ?>"
                               class="regular-text" placeholder="Banner Campaign One">
                        <?php if (array_key_exists('name', $flashMessages['errors'])) { ?>
                            <br/>
                            <span><?php echo $flashMessages['errors']['name'][0]; ?></span>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Update">
        </p>
    </form>
</div>
