<?php
include 'db.php';

$logs = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 50");

while($l = $logs->fetch_assoc()){
    echo "
    <div class='log'>
        <span class='user'>{$l['user']}</span>
        did
        <span class='action'>{$l['action']}</span>
        in
        <span class='module'>{$l['module']}</span>
        <br>
        <small>{$l['created_at']}</small>
    </div>
    ";
}
?>