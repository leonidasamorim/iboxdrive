<?
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $file       = $_GET['url'];
    $file       = str_replace('https:/', 'https://', $file);
    $file       = str_replace('http:/', 'http://', $file);
    $hosturl       = parse_url($file);
    $domainurl  = $hosturl["host"];
    $domain     = $_SERVER['SERVER_NAME'];
    $protocol   = $_SERVER['SERVER_PROTOCOL'];



    if (checkRemoteFile($file)){

        $folderdomain = "files/".$domainurl;
        if (!file_exists($folderdomain)) {
            mkdir($folderdomain);
        }

        file_put_contents($folderdomain ."/". basename($file), fopen($file, 'r'));

        $url = 'http://'. $domain .'/'. $folderdomain. '/'. basename($file);
        Header("Location: ". $url);
        exit;

    }else{
        echo "File not found.";
    }





 function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);
    if($result !== FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}

?>
