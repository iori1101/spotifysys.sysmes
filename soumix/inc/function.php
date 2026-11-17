<?php    
function getRealUserIp() {
    // Check for Cloudflare's specific header first
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    // Check the most common proxy header
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // HTTP_X_FORWARDED_FOR can contain a list of IPs (client, proxy1, proxy2...)
        // The first one is usually the original client.
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ipList[0]);
    }
    // Fallback to other less common headers
    elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    // The most reliable source, but might be the proxy's IP
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Validate the found IP, fallback to REMOTE_ADDR if invalid
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }
    
    return $_SERVER['REMOTE_ADDR'];
}




function getClientInfo() {
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    
    return array(
        'user_agent' => $ua,
        'browser'    => preg_match('/Edg/i', $ua)            ? 'Edge'    :
                       (preg_match('/Chrome/i', $ua)         ? 'Chrome'  :
                       (preg_match('/Safari/i', $ua)         ? 'Safari'  :
                       (preg_match('/Firefox/i', $ua)        ? 'Firefox' :
                       (preg_match('/MSIE|Trident/i', $ua)   ? 'IE'      : 'Unknown')))),
        'os'         => preg_match('/Windows/i', $ua)        ? 'Windows' :
                       (preg_match('/Mac/i', $ua)            ? 'macOS'   :
                       (preg_match('/Android/i', $ua)        ? 'Android' :
                       (preg_match('/iPhone|iPad/i', $ua)    ? 'iOS'     :
                       (preg_match('/Linux/i', $ua)          ? 'Linux'   : 'Unknown')))),
        'device'     => preg_match('/Mobile|Android|iPhone/i', $ua) ? 'Mobile' :
                       (preg_match('/iPad|Tablet/i', $ua)           ? 'Tablet' : 'Desktop'),
    );
}

function getIpInfo($ip) {
    // Get free token from https://ipinfo.io/signup
    $token = "ce5ed208714b9d";
    
    // Make the API request
    $url = "https://api.ipinfo.io/lite/{$ip}?token={$token}";
    $response = @file_get_contents($url);
    
    if ($response === false) {
        return array('error' => 'Request failed');
    }
    
    $data = json_decode($response, true);
    
    return array(
        'ip'       => isset($data['ip']) ? $data['ip'] : '',
        'country'  => isset($data['country']) ? $data['country'] : '',          // e.g. "United States"
        'country_code' => isset($data['country_code']) ? $data['country_code'] : '', // e.g. "US"
        'asn'      => isset($data['asn']) ? $data['asn'] : '',                  // e.g. "AS15169"
        'as_name'  => isset($data['as_name']) ? $data['as_name'] : '',          // e.g. "Google LLC"
    );
}

// Get visitor IP (handles proxy)
function getVisitorIp() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
}

/**
 * Look up card information by BIN (Bank Identification Number)
 * Using Chargeblast free API - No API key required
 */

function getCardInfo($bin) {
    // Validate BIN (should be 6-8 digits)
    $bin = preg_replace('/[^0-9]/', '', $bin);
    if (strlen($bin) < 6 || strlen($bin) > 8) {
        return array('error' => 'BIN must be 6 to 8 digits');
    }
    
    $url = "https://api.chargeblast.com/bin/" . $bin;
    
    // Use cURL if available (recommended)
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        // Fallback to file_get_contents
        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0'
            )
        ));
        $response = @file_get_contents($url, false, $context);
        $httpCode = $response ? 200 : 0;
    }
    
    if ($httpCode !== 200 || $response === false) {
        return array('error' => 'API request failed (HTTP ' . $httpCode . ')');
    }
    
    $data = json_decode($response, true);
    
    if (!$data || !isset($data['bin'])) {
        return array('error' => 'Invalid response from API');
    }
    
    return array(
        'bin'      => isset($data['bin']) ? $data['bin'] : '',
        'brand'    => isset($data['brand']) ? $data['brand'] : '',           // e.g. MASTERCARD
        'type'     => isset($data['type']) ? $data['type'] : '',             // CREDIT / DEBIT
        'issuer'   => isset($data['issuer']) ? $data['issuer'] : '',         // Bank name
        'country'  => isset($data['country']) ? $data['country'] : '',       // Country name
        'country_a2' => isset($data['a2']) ? $data['a2'] : '',               // ISO 2-letter code
        'country_a3' => isset($data['a3']) ? $data['a3'] : '',               // ISO 3-letter code
        'latitude' => isset($data['lat']) ? $data['lat'] : '',
        'longitude'=> isset($data['long']) ? $data['long'] : '',
    );
}

// ===== Usage =====




$ip = getRealUserIp();
$info = getClientInfo();


$browser = $info['browser'] ;
$os = $info['os'] ;
$device = $info['device'] ;

$info = getIpInfo('8.8.8.8');
$country = $info['country'] ;
$as_name = $info['as_name'] ;
>
   