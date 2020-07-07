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

Bypass
======

All bypasses are saved inside the ``ips`` database.

Each record with ``bypass`` type has the following properties:

- ``Host``: it can be a firewall object or a raw IP/CIDR address
- ``status``: it can be ``enabled`` or ``disabled``
- ``Description``: optional description

Example: ::

  bypass1=bypass
    Description=
    Host=host;test1
    status=enabled
  bypass2=bypass
    Description=
    Host=192.168.0.1
    status=disabled

Custom rules
============

If a file named ``/etc/suricata/rules/custom.rules``, it will be included inside Suricata configuration.
After creating the file, remember to add it to the configuration backup: ::

  echo /etc/suricata/rules/custom.rules >> /etc/backup-config.d/custom.include

Troubleshooting
===============

When troubleshooting network traffic, just remember that Suricata will intercept all the traffic.
