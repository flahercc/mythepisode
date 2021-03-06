#!/usr/bin/perl
############################################################################
#
# File     : grabshowsall.pl
# Usage    : ./grabshowsall.pl <path to shows.txt> <countries>
# Url      : $URL$
# Date     : $Date$
# Revision : $Revision$
# Author   : $Author$
# License  : GPL
#
# Status Codes:
# 0 - Never aired
# 1 - Returning series
# 2 - Canceled/ended
# 3 - TBD/on the bubble
# 4 - In development
# 5 -
# 6 - 
# 7 - New Series
# 8 - Never aired
# 9 - Final season
# 10 - On hiatus
# 11 - Pilot ordered
# 12 - Pilot rejected
############################################################################
use LWP::Simple;
use strict;

my $outFile     = $ARGV[0];
my $countryFile = $ARGV[1];
my @aoi         = "";
my $current     = 0;
my $stripshow   = "";
my $shortshow   = "";
my $name        = "";
my $country     = "";
my $status      = "";
my @array       = ();
my @array2      = ();
my $count       = 0;

if ($#ARGV != 1) {
    print "usage: ./grabshowsall.pl <path to shows.txt> <countries>\n";
    print "Ex: ./grabidshowsall.pl /tmp/shows.txt \"US CA\"\n";
    exit 1;
}

## Get a list of shows from tvrage.com
my $shows = get "http://services.tvrage.com/feeds/show_list.php";
if (!$shows) {
    print "Unable to get show info...possible tvrage issues\n";
    exit 1;
}

## If aoi from config.ini is defined then use it else set to US
if (@aoi) {
    @aoi = split /\s+/, "$ARGV[1]";
}else{
    @aoi = ("US");
}

foreach my $show (split("\n",$shows) ) {
    if ($show =~ m#<(name)>(.*)</\1>#) {
        $name = $2;
        $name =~ s/\&amp\;/\&/g; 
        #print "Name   : $2\n";
    }
    if ($show =~ m#<(country)>(.*)</\1>#) {
        $country = $2;
        #print "Country: $2\n";
    }
    if ($show =~ m#<(status)>(.*)</\1>#) {
        $status = $2;
        if (($status == "1") || ($status == "7") || ($status == "9")) {
            $status = 1;
        }elsif (($status == "2") || ($status == "3") || 
                ($status == "4") || ($status == "5") || 
                ($status == "6") || ($status == "8") || 
                ($status == "10") || ($status == "11") || ($status == "12")) {
            $status = 0;
        }
        #print "Status: $2\n";
    }
    if (grep {$_ eq $country} @aoi) {
        $stripshow = $name;
        $stripshow =~ s/The //g;
        $stripshow =~ s/\W//g;
        $stripshow = lc($stripshow);
        $name =~ s/\"//g;
        $shortshow = $name;
        $shortshow =~ s/ \($country\)//g;
        if ($shortshow =~ /\, The/) {
            $shortshow =~ s/\, The//g;
            $shortshow =~ s/$shortshow/The $shortshow/g;
        }
        if ($shortshow =~ /\, A/) {
            $shortshow =~ s/\, A//g;
            $shortshow =~ s/$shortshow/A $shortshow/g;
        }
        push(@array2, "$stripshow\t$shortshow\t$name\t$status\n");
		
        $count++;
    }
}

@array2 = sort(@array2);

## Write data to shows.txt file
open FILE, ">$outFile" or die $!;
binmode FILE, ":utf8";

foreach my $show (@array2) {
    print FILE "$show";
}

close(FILE);
