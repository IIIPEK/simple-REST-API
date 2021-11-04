Introduction

API can:

add organisations (as sample)in list with relations into MySQL database tables
takes list of all organisations in databes or list relations for selected organisation, with support pagination

Overview

Max numbers of records per page can be 100,
Min numbers of records per page can be 1.

If pages out of range records user recieve status 416 'Requested Range Not Satisfiable'

From names of organisations trimmed first and last spaces, comparison is case insensitive.

If in DB exist organization “Banana Tree”, then “Banana tree”, “Banana Tree    ” or “BANANA Tree” can’t be added, but their relationship will be linked to exist

Authentication

Not supported

Error Codes

The all list of Error Codes can be viewed int /api/shared/Errors.php

GET

Request Params
№	Parameters	Description	Default
1	“p” or “P” 	Page number	1
2	“r” or “R”	Records per page	100
3	“name”	Name searching organisation	


mysite-dev.com/api/orgs/?name=Name_of_organisation&p=1&rp=100

Get relations for one organisation by name “Name_of_organisation” with pagination in JSON.

GET

mysite-dev.com/api/orgs/?p=1&rp=100

Read organisations list with pagination in JSON

POST
http://mysite-dev.com/api/orgs/

Add organisations list from posted JSON

You can use /api/shared/test.json for test POST request.
