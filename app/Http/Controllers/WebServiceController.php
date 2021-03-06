<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;

class WebServiceController extends Controller
{
    const LIMIT_SIZE_FILE   = 10000000;
    
    public function index(Request $request)
    {
        $server     = $request->getHttpHost();
        $version    = $this->getVersion();
        $protocol   = $this->protocol($request);

        return view('home', get_defined_vars());
    }

    public function put(Request $request)
    {
        $file       = $request->get('url');
        if (!isset($file)) {
            $fileGet    = str_replace('/put/', '',$request->getRequestUri());
        }

        if (isset($fileGet)) {
            $queryString = parse_url($request->getRequestUri(), PHP_URL_QUERY);
            $file = $fileGet;
        } else {
            $queryString = parse_url($file, PHP_URL_QUERY);
        }

        $file               = str_replace('https:/', 'https://', $file);
        $file               = str_replace('http:/', 'http://', $file);
        $file               = str_replace('///', '//', $file);
        $fileBase           = explode("?", $file);
        $file               = $fileBase[0];

        $hostUrl            = parse_url($file);
        $domainUrl          = $hostUrl["host"];
        $domain             = $request->getHttpHost();
        $folderDomain       = "get/" . $domainUrl;
        $folderDir          = $folderDomain . "" . dirname($hostUrl["path"]);
        $protocol           = $this->protocol($request);

        if (file_exists($folderDir . "/" . basename($file))) {
            $url = $protocol . '://' . $domain . '/' . $folderDir . '/' . basename($file);

            Header("Location: " . $url . "?origin=cache");
            exit;
        }
        
        $filesize = $this->checkFileSize($file);

        if ($filesize > WebServiceController::LIMIT_SIZE_FILE) {
            echo "Size larger than allowed limit. Your File:" . $filesize . " - Limit: " . WebServiceController::LIMIT_SIZE_FILE;
            exit;
        }

        if ($this->checkRemoteFile($file)) {
            
            if (!file_exists($folderDir)) {
                mkdir($folderDir, 0777, true);
            }

            $fileGet = $file . "?" . $queryString;

            $urlWebFile = $folderDir . "/" . basename($file);
            file_put_contents($urlWebFile, fopen($fileGet, 'r'));

            $url = $protocol . '://' . $domain . '/' . $folderDir . '/' . basename($file);

            Header("Location: " . $url . "?origin=new");
            exit;
        } else {
            echo "File not found.";
        }
    }




    public function checkRemoteFile($url)
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

    public function checkFileSize($url)
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

    public static function getVersion()
    {
        return substr(file_get_contents('../.git/refs/heads/master'),0,8);
    }

    public function protocol($request) {
        return  ($request->getHttpHost() == 'iboxdrive.tk') ?  $protocol = 'https' :  $protocol = 'http';
    }

}
