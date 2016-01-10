<?php
if (isset($_GET['company_id']) && (int)$_GET['company_id'] > 0)
    system('php artisan meetings:list ' . (int)$_GET['company_id']);
else
    system('php artisan meetings:list');
?>
