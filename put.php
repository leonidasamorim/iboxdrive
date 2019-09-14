<?
    error_reporting(0);
    ini_set('display_errors', 0);

    $file       = $_REQUEST['url'];
    $file       = str_replace('https:/', 'https://', $file);
    $file       = str_replace('http:/', 'http://', $file);
    $file       = str_replace('///', '//', $file);
    $filebase   = explode("?", $file);
    $file       = $filebase[0];

    $hosturl    = parse_url($file);
    $domainurl  = $hosturl["host"];
    $domain     = $_SERVER['SERVER_NAME'];
    $protocol   = $_SERVER['SERVER_PROTOCOL'];
    $folderdomain = "get/".$domainurl;

    if (file_exists($folderdomain."/".basename($file))) {
        $url = 'http://'. $domain .'/'. $folderdomain. '/'. basename($file);

        Header("Location: ". $url."?origin=cache");
        exit;
    }


    if (checkRemoteFile($file)){

        if (!file_exists($folderdomain)) {
            mkdir($folderdomain);
        }

        file_put_contents($folderdomain ."/". basename($file), fopen($file, 'r'));

        $url = 'http://'. $domain .'/'. $folderdomain. '/'. basename($file);


        Header("Location: ". $url."?origin=new");
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
