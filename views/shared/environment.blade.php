<?php
$env = \Sinevia\Registry::get("ENVIRONMENT");
?>
<style>
    .environment{
        background: rgba(255,255,255,0.5);
    }
    .environment.local{
        color:green !important;
    }
    .environment.live{
        color:red !important;
    }
</style>
<div class="environment <?php echo $env; ?>" style="position:fixed; left:10px; bottom: 10px;border-radius: 5px;padding:2px 5px;font-size: 10px;color:white;">
    <?php echo $env; ?>
</div>