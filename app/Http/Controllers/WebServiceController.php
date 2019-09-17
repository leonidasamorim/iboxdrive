<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebServiceController extends Controller
{
    const LIMIT_SITE            = 1000000;
    const FOLDER_PERMISSION     = '0777';

    public function index(Request $request)
    {
        $protocol              = 'http';
        $url                   = $request->get('url');

        if ($_SERVER['SERVER_NAME'] == 'iboxdrive.tk') $protocol = 'https';



        return view('home', get_defined_vars());
    }


    public function put()
    {
        echo "metodo put";
    }

}
