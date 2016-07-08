<?php

namespace plunner\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use plunner\Http\Requests;
use plunner\Meeting;

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
        $meeting = Meeting::findOrFail($meetingId);
        //$this->authorize($meeting);
        $ret = self::getImg($meeting);
        $blank = storage_path('img/meetings.jpg');
        if ($ret === false)
            return (new Response(file_get_contents($blank), 200))
                ->header('Content-Type', 'image/jpeg');
        return (new Response($ret, 200))
            ->header('Content-Type', 'image/jpeg');
    }

    private static function getImg(Meeting $meeting)
    {
        $name = 'meetings/' . $meeting->id;
        if (!\Storage::exists($name))
            return false;
        return \Storage::get($name);
    }
}
