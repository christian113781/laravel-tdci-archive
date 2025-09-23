<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\RequestMail;
use Illuminate\Support\Facades\Mail;

class sendRequestEmail extends Controller
{
    public function sendEmail(){
        Mail::to('cfmark407@gmail.com')->send(new RequestMail());
        return  "Email Sent Successfully";
    }
}