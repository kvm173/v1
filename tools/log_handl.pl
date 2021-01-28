#!/usr/bin/perl
use DBI;

$log_out="/var/log/dom.log";
$str=$ARGV[0];
open(FF,">>$log_out");

# 2020-12-14T14:54:22.000000+10:00 10.154.192.21 DKS1512x_rev2.2.1[186882303D00] RTSP client 10.154.192.23 incoming$
@arr=split(/\s+/,$str);

$dt=shift(@arr); $dt=~ s/T/ /; $dt=~ s/\+/ \+/;
$ip=shift(@arr);
$mo=shift(@arr);

# my $driver  = "Pg";
# my $dsn = qq(DBI:$driver:dbname = "dom";host = 127.0.0.1;port = 5432);
# my $userid = "postgres";
# my $password = "postgres";
# my $dbh = DBI->connect($dsn, $userid, $password, { RaiseError => 1 }) or die $DBI::errstr;

$str= join(' ',@arr);
# Opening door by RFID 000350076D7C4, apartment 100
if ($str=~ /Opening door by RFID [a-fA-F\d+]{12,}\, apartment ([\d\w]+)$/) {

$stmt=qq(insert into events (dt,action,id_domophon,id_user,comment,event_type) 
(select '$dt'::timestamptz,'OpenDoorRFYD',d.id, u.id,'$str',2 from users u, users_domophons ud, domophons d 
where appart='$1' and ud.id_user=u.id and ud.id_domophon=d.id and d.ip_addr='$ip'););

}

#CMS handset call started for apartment 100.
elsif ($str=~ /CMS handset call started for apartment ([\d\w]+)\.$/) {
$stmt=qq(insert into events (dt,action,id_domophon,id_user,comment,event_type)
(select '$dt'::timestamptz,'CMShandsetCallStarted',d.id, u.id,'$str',2 from users u, users_domophons ud, domophons d
where appart='$1' and ud.id_user=u.id and ud.id_domophon=d.id and d.ip_addr='$ip'););

}

#CMS handset talk started for apartment 100.
elsif ($str=~ /CMS handset talk started for apartment ([\d\w]+)\.$/) {
$stmt=qq(insert into events (dt,action,id_domophon,id_user,comment,event_type) 
(select '$dt'::timestamptz,'CMShandsetTalkStarted',d.id, u.id,'$str',2 from users u, users_domophons ud, domophons d 
where appart='$1' and ud.id_user=u.id and ud.id_domophon=d.id and d.ip_addr='$ip'););

}
#Opening door by CMS handset for apartment 100
elsif ($str=~ /Opening door by CMS handset for apartment ([\d\w]+)$/) {
$stmt=qq(insert into events (dt,action,id_domophon,id_user,comment,event_type)
(select '$dt'::timestamptz,'OpenDoorCMS',d.id, u.id,'$str',2 from users u, users_domophons ud, domophons d
where appart='$1' and ud.id_user=u.id and ud.id_domophon=d.id and d.ip_addr='$ip'););

}

print {FF} "$dt $ip $mo $str\n";
close(FF);

if ($stmt)
{
my $driver  = "Pg";
my $dsn = qq(DBI:$driver:dbname = "dom";host = 127.0.0.1;port = 5432);
my $userid = "postgres";
my $password = "postgres";
my $dbh = DBI->connect($dsn, $userid, $password, { RaiseError => 1 }) or die $DBI::errstr;


my $rv = $dbh->do($stmt) or die $DBI::errstr;
$dbh->disconnect();
}
