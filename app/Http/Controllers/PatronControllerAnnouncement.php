<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement; 
class PatronControllerAnnouncement extends Controller
{
     public function page($id)
    {
       
        $announcement = Announcement::findOrFail($id);

        return view('patron.patron_announcement_page', compact('announcement'));
    }


}
