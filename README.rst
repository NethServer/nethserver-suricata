===================
nethserver-suricata
===================

The IPS (Intrusion Prevention System) module configures Suricata using the netfilter queue (NFQUEUE). 
NFQUEUE is an iptables and ip6tables target which delegate the decision on packets to a userspace software.

All traffic will be analyzed by Suricata itself and events are logged inside ``/var/log/suricata/eve.json``.
See EveBox for a report of blocking and alerting rules.

Suricata rules are managed by Pulledpork.

Manually enable/disable Suricata
================================

Enabling: ::

  config setprop suricata status enabled
  signal-event firewall-adjust
  signal-event nethserver-suricata-save

Disabling: ::

  config setprop suricata status disabled
  signal-event firewall-adjust
  signal-event nethserver-suricata-save


Troubleshooting
===============

When troubleshooting network traffic, just remember that Suricata will intercept all the traffic.
