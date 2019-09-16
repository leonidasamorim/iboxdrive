<?php
include("config.php");


error_reporting(1);
ini_set('display_errors', 1);

$file = $_REQUEST['url'];
$file = str_replace('https:/', 'https://', $file);
$file = str_replace('http:/', 'http://', $file);
$file = str_replace('///', '//', $file);
$filebase = explode("?", $file);
$file = $filebase[0];

$hosturl = parse_url($file);
$domainurl = $hosturl["host"];
$domain = $_SERVER['SERVER_NAME'];
$protocol = $_SERVER['SERVER_PROTOCOL'];
$folderdomain = "get/" . $domainurl;


if (file_exists($folderdomain . "/" . basename($file))) {
    $url = $PROTOCOL.'://' . $domain . '/' . $folderdomain . '/' . basename($file);

    Header("Location: " . $url . "?origin=cache");
    exit;
}

$filesize = checkFileSize($file);

if ($filesize > LIMIT_SITE) {
    echo "Size larger than allowed limit. Your File:". $filesize . " - Limit: ". LIMIT_SITE;
    exit;
}

if (checkRemoteFile($file)) {

    if (!file_exists($folderdomain)) {
        mkdir($folderdomain);
    }

    file_put_contents($folderdomain . "/" . basename($file), fopen($file, 'r'));

    $url = $PROTOCOL.'://' . $domain . '/' . $folderdomain . '/' . basename($file);


    Header("Location: " . $url . "?origin=new");
    exit;

} else {
    echo "File not found.";
}


function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);
    if ($result !== FALSE) {
        return true;
    } else {
        return false;
    }
}

function checkFileSize($url)
{
    // Assume failure.
    $result = -1;

    $curl = curl_init($url);

    // Issue a HEAD request and follow any redirects.
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    //curl_setopt($curl, CURLOPT_USERAGENT, get_user_agent_string());

    $data = curl_exec($curl);
    curl_close($curl);

    if ($data) {
        $content_length = "unknown";
        $status = "unknown";

        if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches)) {
            $status = (int)$matches[1];
        }

        if (preg_match("/Content-Length: (\d+)/", $data, $matches)) {
            $content_length = (int)$matches[1];
        }

        // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        if ($status == 200 || ($status > 300 && $status <= 308)) {
            $result = $content_length;
        }
    }

    return $result;
}

?>
