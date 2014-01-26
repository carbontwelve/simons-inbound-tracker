<div class="wrap">
    <h2>
        Referral Links
        <a href="<?php echo admin_url(); ?>admin.php?page=inbound_links_record" class="add-new-h2">Add New Campaign</a>
    </h2>

    <?php include(__DIR__ . '/../elements/flash_messages.php'); ?>

    <ul class="subsubsub">
        <li class="all">
            <a href="<?php echo admin_url(); ?>admin.php?page=inbound_links_index&amp;type=all"
               <?php if ($type === 'all'){ ?>class="current"<?php } ?>>
                All
                <span class="count">(<?php echo $count['all']; ?>)</span>
            </a>
            |
        </li>
        <li class="archive">
            <a href="<?php echo admin_url(); ?>admin.php?page=inbound_links_index&amp;type=stared"
               <?php if ($type === 'stared'){ ?>class="current"<?php } ?>>
                Stared
                <span class="count">(<?php echo $count['stared']; ?>)</span>
            </a>
            |
        </li>
        <li class="trash">
            <a href="<?php echo admin_url(); ?>admin.php?page=inbound_links_index&amp;type=deleted"
               <?php if ($type === 'deleted'){ ?>class="current"<?php } ?>>
                Trash
                <span class="count">(<?php echo $count['trash']; ?>)</span>
            </a>
        </li>
    </ul>

    <style>
        .positive-trend{
            color: green;
        }
        .negative-trend{
            color: red;
        }
        .no-trend{
            color: grey;
        }
    </style>

    <table class="wp-list-table widefat fixed buttons" cellspacing="0">
        <thead>
        <tr>
            <th scope="col" id="cb" class="manage-column column-cb check-column">
                <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                <input id="cb-select-all-1" type="checkbox">
            </th>
            <th scope="col" id="campaign-name">Campaign Name</th>
            <th scope="col" id="date" style="width:88px;">Created</th>
            <th scope="col" id="hits" style="width:70px;text-align: center;">Keywords</th>
            <th scope="col" id="hits" style="width:70px;text-align: center;">Clicks</th>
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
                <td style="text-align: center;"><?php echo $row->keywords; ?></td>
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

                    <?php if (is_null($row->deleted_at)) { ?>
                    <a href="<?php echo admin_url(); ?>admin.php?page=inbound_links_record&amp;action=edit&amp;id=<?php echo $row->id; ?>">
                        Edit
                    </a>
                    <?php }else{ ?>
                        Edit
                    <?php } ?> |

                    <a href="<?php echo admin_url(); ?>admin.php?page=inbound_links_keywords&amp;id=<?php echo $row->id; ?>">
                        View Keywords
                    </a> |

                    <?php if (is_null($row->deleted_at)) { ?>

                        <?php if ($row->stared < 1){ ?>

                        <a class="submitdelete" href="<?php echo admin_url(
                        ); ?>admin.php?page=inbound_links_index&amp;action=star&amp;id=<?php echo $row->id; ?>">
                            Star
                        </a>

                        <?php }else{ ?>

                        <a class="submitdelete" href="<?php echo admin_url(
                        ); ?>admin.php?page=inbound_links_index&amp;action=unstar&amp;id=<?php echo $row->id; ?>">
                            Unstar
                        </a>

                        <?php } ?>


                    <?php }else{ ?>
                    Star
                    <?php } ?> |

                    <?php if ($row->stared < 1) { ?>
                        <?php if (is_null($row->deleted_at)) { ?>
                            <a class="submitdelete" href="<?php echo admin_url(
                            ); ?>admin.php?page=inbound_links_index&amp;action=trash&amp;id=<?php echo $row->id; ?>">
                                Trash
                            </a>
                        <?php } else { ?>
                            <a class="submitdelete" href="<?php echo admin_url(
                            ); ?>admin.php?page=inbound_links_index&amp;action=untrash&amp;id=<?php echo $row->id; ?>">
                                Untrash
                            </a>
                        <?php } ?>
                    <?php }else{ ?>
                        Trash
                    <?php } ?>

                </td>
            </tr>

        <?php } }else{ ?>
            <tr>
                <td colspan="7">No campaigns in database.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <select name="action2">
                <option value="-1" selected="selected">Bulk Actions</option>
                <option value="star" class="hide-if-no-js">Star</option>
                <option value="trash">Move to Trash</option>
            </select>
            <input type="submit" name="" id="doaction2" class="button action" value="Apply">
        </div>
        <div class="alignleft actions">
        </div>
        <div class="tablenav-pages <?php if ($pagination['lastPage'] == 0 || $pagination['lastPage'] == 1){ echo 'one-page'; } ?>"><span class="displaying-num"><?php echo $pagination['count']; ?> items</span>
            <span class="pagination-links">
                <a class="first-page <?php if ( $pagination['currentPage'] == 1 ){ ?>disabled<?php } ?>" title="Go to the first page" href="admin.php?page=button_board_index&amp;type=<?php echo $type; ?>">«</a>
                <a class="prev-page <?php if ( ($pagination['currentPage'] - 1) < 1 ) { ?>disabled<?php } ?>" title="Go to the previous page" href="admin.php?page=button_board_index&amp;paged=<?php echo $pagination['currentPage'] - 1; ?>&amp;type=<?php echo $type; ?>">‹</a>
                <span class="paging-input"><?php echo $pagination['currentPage']; ?> of <span class="total-pages"><?php echo $pagination['lastPage']; ?></span></span>
                <a class="next-page <?php if ( ($pagination['currentPage'] + 1) > $pagination['lastPage'] ) { ?>disabled<?php } ?>" title="Go to the next page" href="admin.php?page=button_board_index&amp;paged=<?php echo $pagination['currentPage'] + 1; ?>&amp;type=<?php echo $type; ?>">›</a>
                <a class="last-page <?php if ( $pagination['lastPage'] == 1 || ( $pagination['currentPage'] == $pagination['lastPage'] ) ){ ?>disabled<?php } ?>" title="Go to the last page" href="admin.php?page=button_board_index&amp;paged=<?php echo $pagination['lastPage']; ?>&amp;type=<?php echo $type; ?>">»</a>
            </span>
        </div>
        <br class="clear">
    </div>

</div>
