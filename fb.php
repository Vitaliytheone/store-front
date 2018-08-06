<?php
$hosts = [];
$handle = fopen(__DIR__. "/fb.txt", "r");
while (!feof($handle)) {
    $buffer = fgets($handle, 4096);
    $hosts[] = $buffer;
}
fclose($handle);

if (!isset($argv[1])) {
    echo "Invalid arguments";
    exit;
}

$url = $argv[1];


$url = preg_replace_callback("/(www)(\.facebook)/",  function ($matches) {
    return 'm'.$matches[2];
}, $url);
$u = $url;

getStartCount($u);

function getStartCount($u) {
    $value = -1;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $u);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Language: ru']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36');
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    $output = curl_exec($ch);
    $pattern = "/reduced_like_count\":\"([^\"]+)\"/";
    $matches = array();
    if (preg_match($pattern, $output, $matches)) {
        $pattern = "/([0-9]+).*тыс/";
        $value =  unicodeString($matches[1]);
        if ($s = preg_match($pattern, $value, $matches)) {
            $value = ((int) $matches[1]) * 1000;
        }

    }
    echo $value;
    curl_close($ch);
}

function unicodeString($str, $encoding=null) {
    if (is_null($encoding)) $encoding = ini_get('mbstring.internal_encoding');
    return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', create_function('$match', 'return mb_convert_encoding(pack("H*", $match[1]), '.var_export($encoding, true).', "UTF-16BE");'), $str);
}

/**
 * @param array $parts
 * @return string
 */
function build_url(array $parts)
{
    $scheme   = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';
    $host     = ($parts['host'] ?? '');
    $port     = isset($parts['port']) ? (':' . $parts['port']) : '';
    $user     = ($parts['user'] ?? '');
    $pass     = isset($parts['pass']) ? (':' . $parts['pass'])  : '';
    $pass     = ($user || $pass) ? "$pass@" : '';
    $path     = ($parts['path'] ?? '');
    $query    = isset($parts['query']) ? ('?' . $parts['query']) : '';
    $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';
    return implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);
}


