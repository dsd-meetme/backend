@if ($meetings)
Meetings for the next week: <br>
@foreach ($meetings as $meeting)
{{$meeting['title']}} - {{$meeting['start_time']}} <br>
@endforeach
@else
You don't have meetings for the next week
@endif