Summary: NethServer Suricata IPS module
Name: nethserver-suricata
Version: 1.0.0
Release: 1%{?dist}
License: GPL
Source0: %{name}-%{version}.tar.gz

BuildArch: noarch

Requires: suricata
Requires: nethserver-firewall-base, nethserver-pulledpork

Conflicts: nethserver-snort

BuildRequires: nethserver-devtools

%description
Snort IPS module for NethServer.


%prep
%setup -q

%build
%{makedocs}
perl createlinks

%install
rm -rf %{buildroot}
(cd root ; find . -depth -print | cpio -dump %{buildroot})
%{genfilelist} %{buildroot} > %{name}-%{version}-%{release}-filelist
echo "%doc COPYING" >> %{name}-%{version}-%{release}-filelist


%post

%preun

%clean
rm -rf $RPM_BUILD_ROOT

%files -f %{name}-%{version}-%{release}-filelist
%defattr(-,root,root)
%dir %{_nseventsdir}/%{name}-update

%changelog
* Wed Sep 28 2016 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- Replace Snort with Suricata - NethServer/dev#5104


