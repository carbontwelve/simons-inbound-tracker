<?php
    if ( isset($flashMessages) ){
        if ($flashMessages['success'] !== false) { ?>
    <div id="message" class="updated below-h2">
        <p><?php echo $flashMessages['success']; ?></p>
    </div>
<?php
        }
        if ($flashMessages['error'] !== false) { ?>
    <div id="message" class="error below-h2">
        <p><?php echo $flashMessages['error']; ?></p>
    </div>
<?php
        }
    } ?>
