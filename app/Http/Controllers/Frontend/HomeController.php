<?php

namespace App\Http\Controllers\Frontend;

use DateTime;
use App\Models\Cms;
use App\Models\PastEvents;
use Illuminate\Http\Request;
use App\Models\BookingTicket;
use App\Models\UpcomingEvents;
use App\Models\TicketManagement;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
   
    public function index()
    {
        return view('Forented.pages.index');
    }

       public function about()
    {
        return view('Forented.pages.about');
    }

       public function privacypolicy()
    {
        return view('Forented.pages.privacypolicy');
    }
 
     public function termscondition()
    {
        return view('Forented.pages.termscondition');
    }

     public function geniepolicies()
    {
        return view('Forented.pages.geniepolicies');
    }

       public function deleteuser()
    {
        return view('Forented.pages.deleteuser');
    }

    
}
