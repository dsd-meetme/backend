<?php

namespace plunner\Http\Controllers;

use Illuminate\Http\Request;
use plunner\Http\Requests;

class NoAuthController extends Controller
{
    public function __construct()
    {
    }


    //TODO remove this, it is just a tmp fix
    /**
     *
     * Store a newly created resource in storage.
     *
     * @param int $meetingId
     * @return static
     */
    public function showImage($meetingId)
    {
        $meeting = Group::findOrFail($meetingId);
        //$this->authorize($meeting);
        $ret = self::getImg($meeting);
        $blank = storage_path('img/meetings.jpg');
        if ($ret === false)
            return (new Response(file_get_contents($blank), 200))
                ->header('Content-Type', 'image/jpeg');
        return (new Response($ret, 200))
            ->header('Content-Type', 'image/jpeg');
    }

}
