<?php

// Target URL
$target_url = "http://cryptofailures.thm/index.php";

// Possible characters
$charset = implode('', array_merge(
    range('a', 'z'),     // Lowercase letters
    range('A', 'Z'),     // Uppercase letters
    range('0', '9'),     // Digits
    str_split("!\"#$%&'()*+,-./:;<=>?@[\\]^_`{|}~") // Special symbols
));


// Initial User-Agent
$user_agent = str_repeat("A", 256); // Adjusted length
$known_prefix = "guest:" . $user_agent . ":"; // Base structure
$test_string = substr($known_prefix, -8);

// Function to fetch secure_cookie with a specific User-Agent
function get_secure_cookie($user_agent) {
    global $target_url;

    $context = stream_context_create([
        "http" => [
            "method" => "GET",
            "header" =>
                "Host: cryptofailures.thm\r\n" .
                "User-Agent: $user_agent\r\n" .
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8\r\n" .
                "Accept-Language: en-US,en;q=0.5\r\n" .
                "Accept-Encoding: gzip, deflate, br\r\n" .
                "Connection: close\r\n" .
                "Upgrade-Insecure-Requests: 1\r\n"
        ]
    ]);

    // Fetch response
    $response = file_get_contents($target_url, false, $context);

    // Extract "secure_cookie" from headers
    foreach ($http_response_header as $header) {
        if (stripos($header, "Set-Cookie: secure_cookie=") !== false) {
            preg_match('/secure_cookie=([^;]+)/', $header, $matches);
            
            if (!isset($matches[1])) {
                return null;
            }

            // URL-decode the cookie value before using it
            $decoded_cookie = urldecode($matches[1]);

            // Ensure no URL-encoded characters remain
            if (preg_match('/%[0-9A-Fa-f]{2}/', $decoded_cookie)) {
                die("❌ URL-encoded characters detected in secure_cookie!\n");
            }

            return $decoded_cookie;
        }
    }
    return null;
}


// Start brute-force process
$found_text = "";
while (true) {
    echo "\nCurrent known part: {$found_text}\n";

    // Get new secure_cookie for the current prefix
    $secure_cookie = get_secure_cookie($user_agent);
    $user_agent = substr($user_agent, 1);
    if (!$secure_cookie) {
        die("❌ Failed to retrieve secure_cookie!\n");
    }

    echo "✅ Retrieved (Decoded) secure_cookie: $secure_cookie\n";

    // Extract salt
    $salt = substr($secure_cookie, 0, 2);

    // Brute-force the next character
    $found_char = null;
    foreach (str_split($charset) as $char) {
        $test_string_temp = substr($test_string, 1) . $char;
	print($test_string_temp . "\n");
        
        $hashed_test = crypt($test_string_temp, $salt);
        if (str_contains($secure_cookie, $hashed_test)) {
            echo "✅ Found character: $char\n";
            $found_text .= $char;
            $test_string = $test_string_temp;
            echo $test_string . "\n";
            echo $hashed_test . "\n";
            break;
        }
    }
}

?>
