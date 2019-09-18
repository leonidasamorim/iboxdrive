<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;

class WebServiceController extends Controller
{
    const LIMIT_SIZE_FILE   = 10000000;
    const MAJOR = 1;
    const MINOR = 2;
    const PATCH = 3;

    public function index(Request $request)
    {

        ($request->getHttpHost() == 'iboxdrive.tk') ?  $protocol = 'https' :  $protocol = 'http';

        $version = WebServiceController::getVersion();

        return view('home', get_defined_vars());
    }


    public function put(Request $request)
    {

        $file       = $request->get('url');
        if (!isset($file)) {
            $fileget    = str_replace('/put/', '',$request->getRequestUri());
        }

        ($request->getHttpHost() == 'iboxdrive.tk') ?  $protocol = 'https' :  $protocol = 'http';

        if (isset($fileget)) {
            $querystring = parse_url($request->getRequestUri(), PHP_URL_QUERY);
            $file = $fileget;
        } else {
            $querystring = parse_url($file, PHP_URL_QUERY);
        }

        $file       = str_replace('https:/', 'https://', $file);
        $file       = str_replace('http:/', 'http://', $file);
        $file       = str_replace('///', '//', $file);
        $filebase   = explode("?", $file);
        $file       = $filebase[0];


        $hosturl            = parse_url($file);
        $domainurl          = $hosturl["host"];
        $domain             = $request->getHttpHost();
        $folderdomain       = "get/" . $domainurl;
        $folderdomain_dir   = $folderdomain . "" . dirname($hosturl["path"]);

        if (file_exists($folderdomain_dir . "/" . basename($file))) {
            $url = $protocol . '://' . $domain . '/' . $folderdomain_dir . '/' . basename($file);

            Header("Location: " . $url . "?origin=cache");
            exit;
        }


        $filesize = $this->checkFileSize($file);

        if ($filesize > WebServiceController::LIMIT_SIZE_FILE) {
            echo "Size larger than allowed limit. Your File:" . $filesize . " - Limit: " . WebServiceController::LIMIT_SIZE_FILE;
            exit;
        }

        if ($this->checkRemoteFile($file)) {

            $storage_folder = $folderdomain_dir;


            if (!file_exists($storage_folder)) {
                mkdir($storage_folder, 0777, true);
            }


            $fileget = $file . "?" . $querystring;

            $urlwebfile = $folderdomain_dir . "/" . basename($file);
            file_put_contents($urlwebfile, fopen($fileget, 'r'));

            $url = $protocol . '://' . $domain . '/' . $folderdomain_dir . '/' . basename($file);


            Header("Location: " . $url . "?origin=new");
            exit;

        } else {
            echo "File not found.";
        }



        echo $folderdomain_dir;
        exit;
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
        $commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));

        $commitDate = new \DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
        $commitDate->setTimezone(new \DateTimeZone('UTC'));

        return sprintf('v%s.%s.%s-dev.%s (%s)', self::MAJOR, self::MINOR, self::PATCH, $commitHash, $commitDate->format('Y-m-d H:i:s'));
    }

}
