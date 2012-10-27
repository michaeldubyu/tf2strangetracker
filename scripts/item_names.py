#!/usr/local/bin/python
# -*- coding: utf-8 -*-

import MySQLdb as mdb
import sys
import time
from xml.etree import ElementTree as et
import pycurl
import StringIO
from threading import Thread

#select all distinct steamids from item_table
#iterate through, download their profile
#add name to database for those entries

key = "E952EF69C4972394EF63800AC3F40C07"

con = None
try:
    con = mdb.connect('localhost', 'geogaddi_tf2db',
       'h1myn4meISroot', 'geogaddi_tf2db');
    con.set_character_set('utf8')
    cur = con.cursor()
    cur.execute('SET NAMES utf8;')
    cur.execute('SET CHARACTER SET utf8;')
    cur.execute('SET character_set_connection=utf8;')

    cur.execute("SELECT DISTINCT(`item_defindex`) FROM `item_table`")

    data = cur.fetchall()

    schema = et.parse('schema.xml')

    for row in data:
        defindex = str(row[0])

        for item in schema.findall("./items/item"):
            if (item.find("defindex").text==defindex):
                item_name = item.find("item_name").text
                item_name = item_name.upper()

                cur.execute("UPDATE item_table SET `item_name`=%s WHERE `item_defindex`=%s" ,(item_name,defindex))
                con.commit()

except mdb.Error, e:

    print "Error %d: %s" % (e.args[0],e.args[1])
    sys.exit(1)

finally:

     if con:
         con.close()

