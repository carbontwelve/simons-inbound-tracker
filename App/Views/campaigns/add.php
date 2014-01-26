<div class="wrap">
    <h2>
        Add New Campaign
    </h2>

    <?php include(__DIR__ . '/../elements/flash_messages.php'); ?>

    <form method="post" action="<?php echo admin_url(); ?>admin.php?page=inbound_links_add&amp;action=create">
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><label for="author">Name</label></th>
                <td>
                    <input name="author" type="text" id="author"
                           value="<?php echo(isset($flashMessages['inputs']['author']) ? $flashMessages['inputs']['author'] : ''); ?>"
                           class="regular-text" placeholder="Banner Campaign One">
                    <?php if (array_key_exists('author', $flashMessages['errors'])) { ?>
                        <br/>
                        <span><?php echo $flashMessages['errors']['author'][0]; ?></span>
                    <?php } ?>
                </td>
            </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Create">
        </p>
    </form>
</div>
