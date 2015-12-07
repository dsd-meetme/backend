##SET

set Users;
set Meetings;
param n;
param m; ##Max duration
set TimeSlots := 1..n;
set TimeSlotsLong := 1..(n+m);

##PARAM
param UsersAvailability {Users, TimeSlotsLong};
param MeetingsAvailability {Meetings, TimeSlotsLong};
param UserMeetings {Users, Meetings};
param MeetingDuration {Meetings};

##VAR
var x {Users, Meetings} binary;
var y {Meetings, TimeSlotsLong} binary; ## the starting timeslot

## OBJ
maximize UsersMeetings:
sum{i in Users} sum{j in Meetings} (x[i, j] * MeetingDuration[j]);


## CONST
subject to OneTimeSlot {i in Meetings}:
sum{j in TimeSlotsLong} y[i, j] <= 1;


subject to UsersAvailabilityC {i in Users, j in Meetings, z in TimeSlots, t in {1..MeetingDuration[j]}}:
UsersAvailability[i, z+t-1]+1 >= x[i, j]+y[j, z];

subject to UsersOverlaysMeetings {i in Users, j in Meetings, z in TimeSlots, j2 in Meetings, t in {1..MeetingDuration[j]}:  j2 <> j}:
(x[i, j]+y[j, z] + x[i, j2]+y[j2, z+t-1]) <=3 ;


subject to UserMeetingsC {i in Users, j in Meetings}:
x[i,j] <= UserMeetings[i, j];

subject to MeetingDurationAvailability {i in Meetings, j in TimeSlots, t in {1..MeetingDuration[i]}}:
MeetingsAvailability[i, j+t-1] >= y[i, j];

subject to UserRealMeeting {i in Users, j in Meetings}:
x[i,j] <= sum{z in TimeSlots}y[j,z];


solve;

printf "results:\n";
printf {i in Meetings, j in TimeSlots} " %s %i %i\n", i, j, y[i,j];
printf {i in Users, j in Meetings} " %s %s %i\n", i, j, x[i,j];
printf "\n";


data;

set Users := User1 User2 User3;
set Meetings := Meeting1 Meeting2;
param n := 4;
param m := 4;
##set TimeSlots := 1 2 3 4;

param	UsersAvailability:
1 2 3 4 5 6 7 8:=
User1	1	1	0	0	0	0	0	0
User2	1	1	1	0	0	0	0	0
User3	1	1	1	0	0	0	0	0
;

param	MeetingsAvailability:
1 2 3 4 5 6 7 8:=
Meeting1	1	0	0	0	0	0	0	0
Meeting2	1	1	1	0	0	0	0	0
;

param	UserMeetings:
Meeting1 Meeting2:=
User1	1	0
User2	1	1
User3	1	1
;

param	MeetingDuration:=
Meeting1	1
Meeting2	3
;

end;
