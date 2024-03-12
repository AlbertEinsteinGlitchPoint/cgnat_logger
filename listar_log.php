<?php
$dir = '/var/log/mikrotik/';
$files = scandir($dir);

echo '<select name="logFile">';
foreach ($files as $file) {
    if (strpos($file, '.log') !== false) {
        echo "<option value='$file'>$file</option>";
    }
}
echo '</select>';
?>
