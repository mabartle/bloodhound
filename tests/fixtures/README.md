# Synthetic PCAP fixture

Generate the fixture locally:

```sh
python3 -m pip install scapy
python3 tests/fixtures/generate_sample_pcap.py
```

The generated file `bloodhound-synthetic.pcap` is intentionally not committed. It can be regenerated from the script and is excluded by the repository's ignore rules for PCAP files.

The fixture contains invented traffic only:
- TCP handshake and HTTP GET for `example.test`
- DNS query for `example.test`
- Documentation-only IP ranges (192.0.2.0/24, 198.51.100.0/24, 203.0.113.0/24)

It is suitable for smoke tests of packet decoding and basic flow extraction, not for validating every enrichment or alert feature.
