import MySQLdb as mdb

con = mdb.connect('localhost','geogaddi_tf2db','h1myn4meISroot','geogaddi_tf2db')

steamid=1
itemid=2
kills=3
time=4

with con:

    cur = con.cursor() 
    cur.execute("INSERT INTO `events_201209` (`steamid`,`itemid`,`value`,`time`) VALUES (%s,%s,%s,%s)", (steamid,itemid,kills,time))

con.close
