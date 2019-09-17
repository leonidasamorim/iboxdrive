<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;

class WebServiceController extends Controller
{

    public function index(Request $request)
    {

        $data = $request->toCollection();

        $protocol              = 'http';
        $url                   = $request->get('url');

        if ($_SERVER['SERVER_NAME'] == 'iboxdrive.tk') $protocol = 'https';



        return view('home', get_defined_vars());
    }


    public function put(Request $request)
    {

        $data = $request->toCollection();

        dd($data);
        echo "metodo put";
        exit;
    }

}
