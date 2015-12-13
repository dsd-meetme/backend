<?php
if(isset($_GET['company_id']) && is_int($_GET['company_id']) && $_GET['company_id']>0)
    system('php artisan optimise:meetings '.$_GET['company_id'].' --background');
?>
