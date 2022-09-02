Summary: NethServer Suricata IPS module
Name: nethserver-suricata
Version: 2.6.1
Release: 1%{?dist}
License: GPL
Source0: %{name}-%{version}.tar.gz
# Build Source1 by executing prep-sources
Source1: %{name}-ui.tar.gz

BuildArch: noarch

Requires: suricata
Requires: nethserver-firewall-base >= 3.10.0
Requires: nethserver-pulledpork

Conflicts: nethserver-snort

BuildRequires: nethserver-devtools

%description
Snort IPS module for NethServer.


%prep
%setup -q

%build
%{makedocs}
perl createlinks
sed -i 's/_RELEASE_/%{version}/' %{name}.json

%install
rm -rf %{buildroot}

mkdir -p %{buildroot}/usr/share/cockpit/%{name}/
mkdir -p %{buildroot}/usr/share/cockpit/nethserver/applications/
mkdir -p %{buildroot}/usr/libexec/nethserver/api/%{name}/
tar xf %{SOURCE1} -C %{buildroot}/usr/share/cockpit/%{name}/
cp -a %{name}.json %{buildroot}/usr/share/cockpit/nethserver/applications/
cp -a api/* %{buildroot}/usr/libexec/nethserver/api/%{name}/

(cd root ; find . -depth -print | cpio -dump %{buildroot})
%{genfilelist} %{buildroot} --file /etc/sudoers.d/50_nsapi_nethserver_suricata 'attr(0440,root,root)' > %{name}-%{version}-%{release}-filelist
echo "%doc COPYING" >> %{name}-%{version}-%{release}-filelist


%post

%preun

%clean
rm -rf $RPM_BUILD_ROOT

%files -f %{name}-%{version}-%{release}-filelist
%defattr(-,root,root)
%dir %{_nseventsdir}/%{name}-update
%dir %{_nsdbconfdir}/ips
/usr/libexec/nethserver/api/%{name}/
%attr(0440,root,root) /etc/sudoers.d/20_nethserver_suricata

%changelog
* Fri Sep 02 2022 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.6.1-1
- rsync disaster recovery introduces permissions issue - Bug NethServer/dev#6691

* Tue Aug 30 2022 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.6.0-1
- QoS class #16 is not respected - Bug NethServer/dev#6690

* Wed Jul 06 2022 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.3-1
- QoS bandwidth limits ignored with Suricata IPS enabled - Bug NethServer/dev#6681

* Tue Jun 21 2022 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.2-1
- Suricata memory usage during some SMB file transfers - Bug NethServer/dev#6677

* Tue May 24 2022 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.1-1
- Suricata 5 - NethServer/dev#6661

* Tue Jan 18 2022 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.5.0-1
- IPS silently discarding some packets - Bug NethServer/dev#6626

* Wed Nov 24 2021 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.4.0-1
- IPS: bypass encrypted traffic - NethServer/dev#6600

* Tue Jun 22 2021 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.4-1
- Bypass IPS can't be delegated - Bug NethServer/dev#6529

* Mon Jan 11 2021 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.3-1
- UI issue on tables using vue-good-table - Bug NethServer/dev#6390

* Wed Dec 16 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.2-1
- Suricata dashboard needs a long time to be loaded - Bug NethServer/dev#6357

* Wed Nov 25 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.1-1
- Access web applications from port 980 - NethServer/dev#6344
- Intra-zone IPS bypass - Bug NethServer/dev#6342

* Wed Nov 18 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.3.0-1
- New NethServer 7.9.2009 defaults - NethServer/dev#6320

* Thu Oct 08 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.1-1
- Suricata doesn't start on systems with more than 16 CPUs - Bug NethServer/dev#6297

* Fri Oct 02 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.2.0-1
- Improve IPS performances - NethServer/dev#6283

* Tue Aug 25 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.1.2-1
- IPS: can't create raw IP bypasses - Bug NethServer/dev#6245

* Thu Jul 23 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.1.1-1
- IPS local bypass type - Bug NethServer/dev#6238
- IPS: some IPsec traffic blocked - Bug NethServer/dev#6236

* Tue Jul 14 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.1.0-1
- Suricata: add IPS bypass - NethServer/dev#6222

* Thu Jul 02 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 2.0.0-1
- Switch suricata to repeat mode - NethServer/dev#6205
- Human readable numbers in Cockpit dashboards - NethServer/dev#6206

* Mon Oct 28 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.1-1
- Logs page in Cockpit - Bug NethServer/dev#5866

* Tue Oct 01 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.0-1
- Sudoers based authorizations for Cockpit UI - NethServer/dev#5805

* Tue Sep 03 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.2-1
- Cockpit. List correct application version - Nethserver/dev#5819

* Wed Jul 17 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.1-1
- Cockpit: fix IPS enable/disable action (#15)
- Cockpit: update JavaScript dependencies for security issues
- Cockpit: remember last visited route

* Tue May 28 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.0-1
- IPS Cockpit UI - NethServer/dev#5756

* Wed Nov 08 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.1-1
- Suricata rules download error - Bug NethServer/dev#5370

* Fri Oct 06 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.0-1
- EveBox - NethServer/dev#5346

* Wed Jun 07 2017 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1
- suricata HOME_NET should contain RED interfaces - NethServer/dev#5152

* Wed Sep 28 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- Replace Snort with Suricata - NethServer/dev#5104


