#!/usr/bin/env python
import os
import sys
import subprocess
import collections

def run_process(exe,working_dir=""):
    if working_dir == "":
        working_dir = os.getcwd()
    p = subprocess.Popen(exe, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, cwd=working_dir)
    lines = []
    while(True):
        retcode = p.poll() # returns None while subprocess is running
        line = p.stdout.readline().decode().strip('\n')
        lines.append(line)
        if(line == "" and retcode is not None):
            break
    return lines

def get_param(line, char):
    trimmed = line.strip()
    start = trimmed.index(char)
    return trimmed[start+1:], trimmed[:start]

def print_header(wifis):
    offset = 4
    ssidLength = 4 + offset
    frequencyLength = 9 + offset
    speedLength = 5 + offset
    strengthLength = 8 + offset
    securityLength = 8 + offset
    for ssid,wifi in wifis.items():
        _,uuid,frequency,frequency_type,speed,speed_type,strength,security = wifi
        if len(ssid) > ssidLength:
            ssidLength = len(ssid) + offset
        if len(frequency+" "+frequency_type) > frequencyLength:
            frequencyLength = len(frequency+" "+frequency_type) + offset
        if len(speed+" "+speed_type) > speedLength:
            speedLength = len(speed+" "+speed_type) + offset
        if len(strength) > strengthLength:
            strengthLength = len(strength) + offset
        if len(security) > securityLength:
            securityLength = len(security) + offset
    print("SSID".ljust(ssidLength, " ")+"Strength".ljust(strengthLength, " ")+"Speed".ljust(speedLength, " ")+"Frequency".ljust(frequencyLength, " ")+"Security".ljust(securityLength, " "))
    return ssidLength, frequencyLength, speedLength, strengthLength, securityLength

def print_wifi(wifi, lengths):
    ssid,uuid,frequency,frequency_type,speed,speed_type,strength,security = wifi
    ssidLength, frequencyLength, speedLength, strengthLength, securityLength = lengths
    print(ssid.ljust(ssidLength, " ")+strength.ljust(strengthLength, " ")+(speed+" "+speed_type).ljust(speedLength, " ")+(frequency+" "+frequency_type).ljust(frequencyLength, " ")+security.ljust(securityLength, " "))

lines = run_process(["nmcli","dev","wifi","list"])
wifis = {}
for line in lines:
    if not "'" in line:
        continue
    rest, ssid = get_param(line[1:], "'")
    rest, uuid = get_param(rest, " ")
    rest, _ = get_param(rest, " ")
    rest, frequency = get_param(rest, " ")
    rest, frequency_type = get_param(rest, " ")
    rest, speed = get_param(rest, " ")
    rest, speed_type = get_param(rest, " ")
    rest, strength = get_param(rest, " ")
    security = rest.replace(" no", "").replace(" yes", "").strip()
    if ssid in wifis:
        _,_,_,_,_,_,currentStrength,_ = wifis[ssid]
        if int(strength) > int(currentStrength):
            wifis[ssid] = (ssid,uuid,frequency,frequency_type,speed,speed_type,strength,security)
    else:
        wifis[ssid] = (ssid,uuid,frequency,frequency_type,speed,speed_type,strength,security)

lengths = print_header(wifis)
for ssid,wifi in wifis.items():
    print_wifi(wifi, lengths)
