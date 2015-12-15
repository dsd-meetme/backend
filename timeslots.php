<?php
if(isset($_GET['employee_id']) && (int)$_GET['employee_id']>0)
    system('php artisan timeslots:timeslots '.(int)$_GET['employee_id']);

?>
