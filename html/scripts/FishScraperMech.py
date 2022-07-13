from dataclasses import dataclass
import socket
import sys
import mechanicalsoup
import string


"""
sock = socket.socket()
sock.bind(('',80))

while True:
    sock.listen(1)
    connection, address = sock.accept()
    buf = connection.recv(1024)
    if len(buf) > 0:
        input = buf
        break
        
"""

#finds inputs from command line and places them into variables
num_days = len(sys.argv) - 2
location = sys.argv[1]
days = [""] * (num_days)

l = 0

while l < num_days:
    days[l] = sys.argv[l+2]
    l += 1

#location = "Port Waikato"
#Mon Tue Wed Thu Fri Sat Sun
#days = ["Wed", "Thu", "Fri", "Mon"]

#------------------------------------The following code is for SURF-FORECAST website scraping ------------------------------------------------------------------------------------------------------

browser = mechanicalsoup.Browser()
url = "http://www.surf-forecast.com"
page = browser.get(url)
pagesoup = page.soup
form = pagesoup.select("form")[0]
form.select("input")[2]["value"] = location

data_page = browser.submit(form,page.url)
data_soup = data_page.soup

table = data_soup.select("#forecast-table > div.forecast-table__content > table > tbody > tr.forecast-table__row.forecast-table-days")[0]

#first day in table can have 1 2 or 3 entries according to the time of day, need to count how many
#Using is-day-end to find final third of the the first day

i = 1

while i <= 2:
    string = str(data_soup.select("#forecast-table > div.forecast-table__content > table > tbody > tr:nth-child(5) > td:nth-child(" + str(i) + ")")) 
    if "is-day-end" in string:
        break
    i+= 1

#function to extract information for each day given in 'days' list

def SF_info(day):

    #finding which columns have the correct days in them and importing into list
    z = 0
    column = []
    string_table = [int] * len(table)

    for item in table:
        string = str(item)
        string_table[i] = string
        if day in string:
            column.insert(len(column), z)   
        z += 1

    #while loop above reports if the "is-day-end" string is found in the first second or third column (doesn't start from 0)
    #To extract data use above int to find 
    #"#forecast-table > div.forecast-table__content > table > tbody > tr:nth-child(8) > td:nth-child(4) > strong"
    #body.has-sidebar-large.ultrawide.wide-page.has-content-large.current-units-m.locale-en div#up.off-canvas-wrap div#upbg1.inner-wrap div#ucent.main-container div.content-wrapper.lmid div#content.content.with-sticky-banner div#contdiv div.col-reverse div div.has-my-4 div#forecast-table.forecast-table div.forecast-table__content table.js-forecast-table-content.forecast-table__table.forecast-table__table--content tbody.forecast-table__basic tr.forecast-table__row td.forecast-table__cell.forecast-table-energy__cell.is-day-end strong



    day_num = column[0]
    column_num = (3*day_num +1)-(3-i)
    column_num_int = column_num

    y = 0

    if day_num == 0:
        column_num_int = 1
        c = i-1
    else:
        c = 2
    slots = c + 1

    day_wave_height = [float] * slots
    day_wave_power = [int] * slots
    day_high_tide = [str] * slots
    day_low_tide = [str] * slots

    while y <= c:
        #waver heights
        wave_soup = data_soup.select("#forecast-table > div.forecast-table__content > table > tbody > tr:nth-child(5) > td:nth-child(" + str(column_num_int) + ") > div > svg > text")
        wave_height = float(((str(wave_soup)).split(">")[1]).split("<")[0])
        day_wave_height[y] = wave_height

        #wave power
        wave_soup = data_soup.select("#forecast-table > div.forecast-table__content > table > tbody > tr:nth-child(8) > td:nth-child(" + str(column_num_int) + ") > strong")
        wave_power = int(((str(wave_soup)).split(">")[1]).split("<")[0])
        day_wave_power[y] = wave_power

        #high tides
        wave_soup = data_soup.select("#forecast-table > div.forecast-table__content > table > tbody > tr:nth-child(11) > td:nth-child(" + str(column_num_int) + ") > div:nth-child(1)")
        high_tide = ((str(wave_soup).split(">")[1]).split("<")[0]).strip()
        day_high_tide[y] = high_tide

        #low tides
        wave_soup = data_soup.select("#forecast-table > div.forecast-table__content > table > tbody > tr:nth-child(12) > td:nth-child(" + str(column_num_int) + ") > div:nth-child(1)")
        low_tide = ((str(wave_soup).split(">")[1]).split("<")[0]).strip()
        day_low_tide[y] = low_tide

        y += 1
        column_num_int += 1

    r = day_high_tide.count('')
    for x in range(r):
        day_high_tide.remove('')

    r = day_low_tide.count('')
    for x in range(r):
        day_low_tide.remove('')
    #day_high_tide.remove('')
    #day_low_tide.remove('')

    print(day + " information")
    print("AM, PM, Night")
    print(str(day_wave_height) +"  wave heights (m)")
    print(str(day_wave_power) + " wave power (kJ)")
    print(str(day_high_tide) + " high tide")
    print(str(day_low_tide) +" low tide")
    print()
    return(day, day_wave_height, day_wave_power, day_high_tide, day_low_tide)


for input in days:
    day_String =  SF_info(input)
    #string_Return = string_Return + day_String
    #print(day_String)



#----------------------------------------END OF SURF-FORECAST website scrape-----------------------------------------------------------------------------------------------------------------------------------------


