<?php
include("db.php");

$sql = "UPDATE admin_settings SET
    system_name = '{$_POST['system_name']}',
    system_url = '{$_POST['system_url']}',
    timezone = '{$_POST['timezone']}',
    date_format = '{$_POST['date_format']}',
    default_language = '{$_POST['default_language']}',
    items_per_page = '{$_POST['items_per_page']}',

    maintenance_mode = '{$_POST['maintenance_mode']}',
    auto_save = '{$_POST['auto_save']}',
    two_factor_auth = '{$_POST['two_factor_auth']}',

    theme = '{$_POST['theme']}',
    enable_animations = '{$_POST['enable_animations']}',

    notification_email = '{$_POST['notification_email']}',
    notification_schedule = '{$_POST['notification_schedule']}'
";

mysqli_query($conn, $sql);
echo "ok";
