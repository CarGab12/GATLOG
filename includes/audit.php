<?php


function get_client_ip() {
foreach (array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR') as $key) {
if(!empty($_SERVER[$key])) {
$ip = $_SERVER[$key];
$ip = explode(',', $ip)[0];
return trim($ip);
}
}
return null;
}


function log_audit(PDO $pdo, $table_name, $record_id = null, $action = 'other', $old_data = null, $new_data = null) {
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? ($_SESSION['email'] ?? null);



$ip = get_client_ip();
$ua = $_SERVER['HTTP_USER_AGENT'] ?? null;


$sql = "INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, user_id, user_name, ip_address, user_agent, created_at)
VALUES (:table_name, :record_id, :action, :old_data, :new_data, :user_id, :user_name, :ip, :ua, NOW())";
$stmt = $pdo->prepare($sql);


$stmt->execute([
':table_name' => $table_name,
':record_id' => $record_id,
':action' => $action,
':old_data' => $old_data !== null ? json_encode($old_data, JSON_UNESCAPED_UNICODE) : null,
':new_data' => $new_data !== null ? json_encode($new_data, JSON_UNESCAPED_UNICODE) : null,
':user_id' => $user_id,
':user_name' => $user_name,
':ip' => $ip,
':ua' => $ua
]);
}