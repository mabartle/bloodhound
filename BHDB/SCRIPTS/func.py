#!/usr/bin/python

def cleanblacklist():
    wl = open('whitelist.txt','r').read().strip().split('\n')
    bl = open('blacklist.txt','r').read().strip().split('\n')
    updatedblacklist = []
 
    for url in bl:
        if url not in wl:
            updatedblacklist.append(url)
    #return updatedblacklist
test = cleanblacklist()
print test