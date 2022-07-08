<?php

namespace App\Http\Controllers;

use App\Events\MessageSend;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class ChatController extends Controller
{

    public function index(){
        $user = Auth::user();
        return Inertia::render('Chat/Index',compact('user'));
    }
    public function fetchMessage(){
        return Message::with('user')->latest()->paginate(20);
    }
    public function sendMesage(Request $request){
       $message = Message::create([
        'message' => $request->message,
        'user_id' => Auth::user()->id
       ]);
       $user= Auth::user();
       broadcast(new MessageSend($user,$message))->toOthers();
       return $message->load('user');
    }
}
