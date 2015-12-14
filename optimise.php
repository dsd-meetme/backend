<?php
if(isset($_GET['company_id']) && (int)$_GET['company_id']>0)
    system('php artisan optimise:meetings '.(int)$_GET['company_id']);
?>
