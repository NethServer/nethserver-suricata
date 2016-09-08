===
IPS
===

Configure the IPS (Intrusion Prevention System) behavior. 
The IPS will analyze all traffic through the firewall, looking for 
possible attacks and policy violations. 

Depending on the configuration, the IPS can report a threat or block involved traffic.

Enabled/Disabled
    Enable or disable the IPS

Rule policy
   Select the group of rules to be applied. There are 4 policy: 

   * Connectivity: check a large number of vulnerabilities, it does not impact on non-realtime applications (eg VoIP) 
   * Balanced: suitable for most scenarios, it is a good compromise between security and usability (recommended) 
   * Security: safe mode but very invasive, may impact on chat applications and peer-to-peer 
   * Expert: the administrator must manually select the rules from the command line
