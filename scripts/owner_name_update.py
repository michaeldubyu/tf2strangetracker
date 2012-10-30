#!/usr/local/bin/python
# -*- coding: utf-8 -*-
# multi-threaded script to update item owner names in the db

import MySQLdb as mdb
import sys
import time
from xml.etree import ElementTree as et
import pycurl
import StringIO
from threading import Thread

#to download the xml of the profile use this subroutine
def get_profile_xml(steamid):

    url = "http://steamcommunity.com/profiles/"+steamid+"/?xml=1"
    c = pycurl.Curl()
    c.setopt(c.URL,url)
    c.setopt(c.CONNECTTIMEOUT, 10)
    c.setopt(c.FOLLOWLOCATION, 1)
    c.setopt(c.TIMEOUT, 10)
    c.setopt(pycurl.NOSIGNAL, 1)
    b = StringIO.StringIO()
    c.setopt(pycurl.WRITEFUNCTION, b.write)
    c.perform()
    return b.getvalue()


def update_name_thread(start,end,data):
    month = time.strftime('%Y%m')
    table = "events_"+month

    con = None
    try:
        con = mdb.connect('localhost', 'geogaddi_tf2db',
           'h1myn4meISroot', 'geogaddi_tf2db');
        con.set_character_set('utf8')
        cur = con.cursor()
        cur.execute('SET NAMES utf8;')
        cur.execute('SET CHARACTER SET utf8;')
        cur.execute('SET character_set_connection=utf8;')

        #for each steamid in the list passed to this thread
        for row in data:
            steamid = str(row[0])
            prof_xml = get_profile_xml(steamid)
            profile = et.fromstring(prof_xml)
            steam_name = profile.findtext('steamID')
            steam_name.encode('utf-8')

            cur.execute("UPDATE item_table SET `owner_name`=%s WHERE `steam_id`=%s" ,(steam_name,steamid))
            con.commit()

    except mdb.Error, e:

        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:

         if con:
             con.close()

#where program actually starts
con = None
try:
    con = mdb.connect('localhost', 'geogaddi_tf2db','h1myn4meISroot', 'geogaddi_tf2db');

    cur = con.cursor()
    cur.execute("SELECT DISTINCT(`steam_id`) FROM `item_table`")

    data = cur.fetchall()

    #partition steam ids up into groups of 200 for each thread to handle
    end = 0
    start = 0
    while end < len(data):
        if (end+200>len(data)):
            start = end
            end += len(data)%200
        else:
            start = end
            end += 200
        t = Thread(target=update_name_thread,args=(start,end,data[start:end]))
        t.start()

except mdb.Error, e:

    print "Error %d: %s" % (e.args[0],e.args[1])
    sys.exit(1)

finally:

     if con:
         con.close()

