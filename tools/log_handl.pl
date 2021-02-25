#!/usr/bin/perl
use MIME::Base64;
use DBI;
use Time::Local;

$log_out="/var/log/dom.log";
$str=$ARGV[0];
# $str="2021-02-24T15:12:51.000000+10:00 10.154.192.21 DKS1512x_rev2.2.1[186882303D00] [69706] Opening door by CMS handset for apartment 100";
# $str="2021-02-24T16:44:51.000000+10:00 10.154.192.21 DKS1512x_rev2.2.1[186882303D00] 000:[42464] SIP call done for apartment 100, handset is down";

open(FF,">>$log_out");

# 2020-12-14T14:54:22.000000+10:00 10.154.192.21 DKS1512x_rev2.2.1[186882303D00] RTSP client 10.154.192.23 incoming$
@arr=split(/\s+/,$str);

$dt=shift(@arr); ## $dt.=shift(@arr); $dt.=shift(@arr);
$dt=~ s/T/ /; $dt=~ s/\+/ \+/;
$ip=shift(@arr);
$mo=shift(@arr);
# print("++$dt++$ip++$mo++\n");

$str= join(' ',@arr);
# Opening door by RFID 000350076D7C4, apartment 100
if ($str=~ /Opening door by RFID [a-fA-F\d+]{10,}\, apartment ([\d\w]+)$/) {
$apart=$1;
$event='OpenDoorRFYD';
}

#CMS handset call started for apartment 100.
elsif ($str=~ /CMS handset call started for apartment ([\d\w]+)\.$/) {
$apart=$1;
$event='CMShandsetCallStarted';
}

#CMS handset talk started for apartment 100.
elsif ($str=~ /CMS handset talk started for apartment ([\d\w]+)\.$/) {
$apart=$1;
$event='CMShandsetTalkStarted';
}

#Opening door by CMS handset for apartment 100
elsif ($str=~ /Opening door by CMS handset for apartment ([\d\w]+)$/) {
$apart=$1;
$event='OpenDoorCMS';
}

# Opening door by DTMF command for apartment 100
elsif ($str=~ /Opening door by DTMF command for apartment ([\d\w]+)$/) {
$apart=$1;
$event='OpenDoorSIP';
}

# Opening alt door by DTMF command for apartment 100
elsif ($str=~ /Opening alt door by DTMF command for apartment ([\d\w]+)$/) {
$apart=$1;
$event='OpenDoorDopSIP';
}

#SIP call done for apartment 100, handset is down
elsif ($str=~ /SIP call done for apartment ([\d\w]+)/) {
$apart=$1;
$event='SIPCallDone';
}

print {FF} "$dt $ip $mo $str\n"; # пишем в стандартный лог всю исходную строку, пришедшую по rsyslog (сознательно слегка исковеркали строку даты)
close(FF);

if ($event) # если есть, что делать работаем с постгресом и апи форпоста
{
my $driver  = "Pg";
my $dsn = qq(DBI:$driver:dbname = "dom";host = 127.0.0.1;port = 5432);
my $userid = "postgres";
my $password = "postgres";
my $dbh = DBI->connect($dsn, $userid, $password, { RaiseError => 1 }) or die $DBI::errstr;
# my $rv = $dbh->do($stmt) or die $DBI::errstr;
$stmt=qq(insert into events (dt,action,id_domophon,id_user,comment,event_type) 
(select '$dt'::timestamptz,'$event',d.id, u.id,'$str',2 from users_domophons u, users_domophons ud, domophons d 
where ud.apart='$apart' and ud.id_user=u.id and ud.id_domophon=d.id and d.ip_addr='$ip'););

# print("$stmt\n");

my $rv = $dbh->do($stmt) or print $DBI::errstr;                  
my $forpost_admin="melnikov-admin";
my $forpost_admin_pass="1232021";
# my $rv = $dbh->do($stmt) or die $DBI::errstr; # записали в локальный постгрес события от домофона

$stmt=qq(SELECT id_forpost FROM public.domophons where ip_addr='$ip';);
$sth = $dbh->prepare($stmt);
$rv = $sth->execute();
$ref = $sth->fetchrow_hashref();
$id_forpost=$ref->{'id_forpost'};
$sth->finish();
print("Camera= $id_forpost Event= $event\n");

# $stmt=qq(SELECT distinct forpost_user,forpost_pass FROM public.users where appart='$apart';);
# $sth= $dbh->prepare($stmt);
# $rv= $sth->execute();
# $ref= $sth->fetchrow_hashref();
# ($forpost_user,$forpost_pass)=($ref->{'forpost_user'},decode_base64($ref->{'forpost_pass'}));
# $sth->finish();
# print("Forpost user= $forpost_user forpost_pass= $forpost_pass\n");

$dbh->disconnect();

## далее апи форпоста
$rr=int(rand(100));
$dt=~ /(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\.\d+ \+(\d+):/;
($tz_dt,$tz_zo,$tz_rand)= (timelocal($6,$5,$4,$3,$2-1,$1),$7,"$1$2$3$4$5$6_$rr");

$str_curl=qq(curl -s -X POST -d "AdminLogin=$forpost_admin&AdminPassword=$forpost_admin_pass&CameraID=$id_forpost&EventID=$tz_rand&StartRecord=10&Name=$event" https://live.vladlink.ru/system-api/AddCustomEvent);
print "++$str_curl++\n";
$ech_curl=`$str_curl`;
print "++$ech_curl++\n";

}
