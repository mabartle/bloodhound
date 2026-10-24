#!/usr/bin/env python3
"""Generate a tiny synthetic PCAP for Bloodhound development tests.

All hosts, domains, and payloads are invented. The packets use documentation-only
IPv4 ranges and the reserved .test domain. No live traffic is captured.

Install dependency: python3 -m pip install scapy
Run: python3 tests/fixtures/generate_sample_pcap.py
Output: tests/fixtures/bloodhound-synthetic.pcap
"""
from pathlib import Path

from scapy.all import DNS, DNSQR, Ether, IP, TCP, UDP, Raw, wrpcap

ROOT = Path(__file__).resolve().parent
OUTPUT = ROOT / "bloodhound-synthetic.pcap"

CLIENT_MAC = "02:00:00:00:00:10"
SERVER_MAC = "02:00:00:00:00:20"
CLIENT_IP = "192.0.2.10"       # TEST-NET-1, documentation only
WEB_IP = "198.51.100.20"       # TEST-NET-2, documentation only
DNS_IP = "203.0.113.53"        # TEST-NET-3, documentation only
CLIENT_PORT = 49152
HTTP_PORT = 80
DNS_PORT = 53

packets = []

def frame(src_mac, dst_mac, src_ip, dst_ip, l4):
    return Ether(src=src_mac, dst=dst_mac) / IP(src=src_ip, dst=dst_ip) / l4

# Synthetic TCP handshake and one HTTP request to example.test.
packets.append(frame(CLIENT_MAC, SERVER_MAC, CLIENT_IP, WEB_IP,
                     TCP(sport=CLIENT_PORT, dport=HTTP_PORT, flags="S", seq=1000)))
packets.append(frame(SERVER_MAC, CLIENT_MAC, WEB_IP, CLIENT_IP,
                     TCP(sport=HTTP_PORT, dport=CLIENT_PORT, flags="SA", seq=2000, ack=1001)))
packets.append(frame(CLIENT_MAC, SERVER_MAC, CLIENT_IP, WEB_IP,
                     TCP(sport=CLIENT_PORT, dport=HTTP_PORT, flags="A", seq=1001, ack=2001)))
request = (
    b"GET /bloodhound-demo HTTP/1.1\r\n"
    b"Host: example.test\r\n"
    b"User-Agent: Bloodhound-Revival-Synthetic/1.0\r\n"
    b"Accept: */*\r\nConnection: close\r\n\r\n"
)
packets.append(frame(CLIENT_MAC, SERVER_MAC, CLIENT_IP, WEB_IP,
                     TCP(sport=CLIENT_PORT, dport=HTTP_PORT, flags="PA",
                         seq=1001, ack=2001) / Raw(load=request)))

# Synthetic DNS query for example.test.
dns_query = DNS(rd=1, qd=DNSQR(qname="example.test", qtype="A"))
packets.append(frame(CLIENT_MAC, SERVER_MAC, CLIENT_IP, DNS_IP,
                     UDP(sport=53000, dport=DNS_PORT) / dns_query))

# Stable, spaced timestamps make the fixture repeatable for time-filter tests.
for i, packet in enumerate(packets):
    packet.time = 1_700_000_000 + i * 2

wrpcap(str(OUTPUT), packets)
print(f"Wrote {len(packets)} synthetic packets to {OUTPUT}")
