##SET

set Users;
table tin1 IN "CSV" "Users.csv" :
Users <- [i];
set Meetings;
table tin2 IN "CSV" "Meetings.csv" :
Meetings <- [i];
param n;
param m; ##Max duration
set TimeSlots := 1..n;
set TimeSlotsLong := 1..(n+m);


##PARAM
param UsersAvailability {Users, TimeSlotsLong};
param MeetingsAvailability {Meetings, TimeSlotsLong};
param UsersMeetings {Users, Meetings};
param MeetingsDuration {Meetings};

table tin3 IN "CSV" "UsersAvailability.csv" :
[i,j], UsersAvailability;

table tin4 IN "CSV" "MeetingsAvailability.csv" :
[i,j], MeetingsAvailability;

table tin5 IN "CSV" "UsersMeetings.csv" :
[i,j], UsersMeetings;

table tin6 IN "CSV" "MeetingsDuration.csv" :
[i], MeetingsDuration;

##VAR
var x {Users, Meetings} binary;
var y {Meetings, TimeSlotsLong} binary; ## the starting timeslot

## OBJ
maximize UsersMeetingsMax:
sum{i in Users} sum{j in Meetings} (x[i, j] * MeetingsDuration[j]);


## CONST
subject to OneTimeSlot {i in Meetings}:
sum{j in TimeSlotsLong} y[i, j] <= 1;


subject to UsersAvailabilityC {i in Users, j in Meetings, z in TimeSlots, t in {1..MeetingsDuration[j]}}:
UsersAvailability[i, z+t-1]+1 >= x[i, j]+y[j, z];

subject to UsersOverlaysMeetings {i in Users, j in Meetings, z in TimeSlots, j2 in Meetings, t in {1..MeetingsDuration[j]}:  j2 <> j}:
(x[i, j]+y[j, z] + x[i, j2]+y[j2, z+t-1]) <=3 ;

subject to UsersMeetingsC {i in Users, j in Meetings}:
x[i,j] <= UsersMeetings[i, j];

subject to MeetingsDurationAvailability {i in Meetings, j in TimeSlots, t in {1..MeetingsDuration[i]}}:
MeetingsAvailability[i, j+t-1] >= y[i, j];

subject to UserRealMeeting {i in Users, j in Meetings}:
x[i,j] <= sum{z in TimeSlots}y[j,z];

solve;

#printf "results:\n";
#printf {i in Meetings, j in TimeSlots} " %s %i %i\n", i, j, y[i,j];
#printf {i in Users, j in Meetings} " %s %s %i\n", i, j, x[i,j];
#printf "\n";

table tout {i in Users, j in Meetings} OUT "CSV" "x.csv" :
i, j, x[i,j];

table tout {j in Meetings, z in TimeSlots} OUT "CSV" "y.csv" :
j, z, y[j,z];

data;

param n := 4;
param m := 4;

end;
