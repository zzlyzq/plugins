#!/usr/bin/make -f

# i-MSCP PhpSwitcher plugin
# Copyright (C) 2014-2015 Laurent Declercq <l.declercq@nuxwin.com>
#
# This library is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public
# License as published by the Free Software Foundation; either
# version 2.1 of the License, or (at your option) any later version.
#
# This library is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
# Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public
# License along with this library; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

# Set this flag to 'yes' if you want to disable all modifications breaking abi
# compatibility to upstream
PHP5_COMPAT=no

# Enable dpkg build flags
DPKG_EXPORT_BUILDFLAGS = 1
include /usr/share/dpkg/buildflags.mk

DEB_HOST_GNU_TYPE    ?= $(shell dpkg-architecture -qDEB_HOST_GNU_TYPE)
DEB_BUILD_GNU_TYPE   ?= $(shell dpkg-architecture -qDEB_BUILD_GNU_TYPE)
DEB_HOST_ARCH        ?= $(shell dpkg-architecture -qDEB_HOST_ARCH)
DEB_HOST_ARCH_OS     ?= $(shell dpkg-architecture -qDEB_HOST_ARCH_OS)
DEB_HOST_MULTIARCH   ?= $(shell dpkg-architecture -qDEB_HOST_MULTIARCH)
export DEB_HOST_MULTIARCH

ifeq ($(DEB_HOST_ARCH),$(filter $(DEB_HOST_ARCH),amd64 armel armhf i386 ia64 powerpc))
  CONFIGURE_DTRACE_ARGS = --enable-dtrace
else
  CONFIGURE_DTRACE_ARGS = --disable-dtrace
endif

PROG_SENDMAIL = /usr/sbin/sendmail

ifeq (,$(findstring noopt,$(PHPSWITCHER_BUILD_OPTIONS)))
  CFLAGS += -O2
else
  CFLAGS += -O0
endif

#CFLAGS += -Wall -fsigned-char -fno-strict-aliasing
CFLAGS += -fsigned-char -fno-strict-aliasing

# LFS support
ifneq (yes,$(PHP5_COMPAT))
  CFLAGS += $(shell getconf LFS_CFLAGS)
endif

# Enable IEEE-conformant floating point math on alphas ( not the default )
ifeq (alpha-linux-gnu,$(DEB_HOST_GNU_TYPE))
  CFLAGS += -mieee
endif

# Enable producing of debugging information
#CFLAGS += -g

# support new (>= 2.2) and older versions of libtool
LIBTOOL_DIRS = /usr/share/libtool/config /usr/share/libtool
LTMAIN = $(firstword $(wildcard $(foreach d,$(LIBTOOL_DIRS),$d/ltmain.sh)))
LTMAIN_DIR = $(dir $(LTMAIN))

# Set number of parallel jobs
ifneq (,$(filter parallel=%,$(PHPSWITCHER_BUILD_OPTIONS)))
	NUMJOBS = $(patsubst parallel=%,%,$(filter parallel=%,$(PHPSWITCHER_BUILD_OPTIONS)))
	MAKEFLAGS += -j$(NUMJOBS)
endif

# install prefix
PREFIX ?= /usr/local

# Template destdir
TPLS_DESTDIR ?= /var/www/imscp/gui/plugins/PhpSwitcher/PhpCompiler/templates

# General config
GENERAL_CONFIG = \
	--build=$(DEB_BUILD_GNU_TYPE) \
	--host=$(DEB_HOST_GNU_TYPE) \
	--prefix=$(PREFIX) \
	--with-config-file-path=$(PREFIX)/etc/php \
	--with-config-file-scan-dir=$(PREFIX)/etc/php/conf.d

# Common config ( apply to all PHP versions - Unknown options are simply ignored )
COMMON_CONFIG = \
	$(GENERAL_CONFIG) \
	--disable-debug \
	--disable-rpath \
	--disable-static \
	--enable-bcmath \
	--enable-calendar \
	--enable-ctype \
	--enable-exif \
	--enable-ftp \
	--enable-gd-native-ttf \
	--enable-intl=shared \
	--enable-mbstring \
	--enable-pcntl \
	--enable-pdo=shared \
	--enable-shmop \
	--enable-soap \
	--enable-sockets \
	--enable-sysvmsg \
	--enable-sysvsem \
	--enable-sysvshm \
	--enable-wddx \
	--enable-zip \
	--with-curl=shared,/usr \
	--with-db4 \
	--with-bz2 \
	--with-enchant=shared,/usr \
	--with-freetype-dir=shared,/usr \
	--with-gettext \
	--with-gd=shared \
	--with-gmp=shared,/usr \
	--with-iconv \
	--with-imap=shared,/usr \
	--with-imap-ssl \
	--with-jpeg-dir=shared,/usr \
	--with-kerberos=/usr \
	--with-layout=GNU \
	--with-libedit=shared \
	--with-libxml-dir=/usr \
	--with-ldap=shared,/usr \
	--with-ldap-sasl=/usr \
	--with-onig=/usr \
	--with-openssl=/usr \
	--with-pcre-regex=/usr \
	--with-mcrypt=shared,/usr \
	--with-mhash=yes \
	--with-mssql=shared,/usr \
	--with-mysql-sock=/var/run/mysqld/mysqld.sock \
	--with-pdo-dblib=shared,/usr \
	--with-pdo-odbc=shared,unixODBC,/usr \
	--with-pdo-pgsql=shared,/usr/bin/pg_config \
	--with-pdo-sqlite=shared,/usr \
	--with-pgsql=shared,/usr PGSQL_INCLUDE=`pg_config --includedir` \
	--with-pic \
	--with-png-dir=shared,/usr \
	--with-pspell=shared,/usr \
	--with-qdbm=shared/usr \
	--with-recode=shared,/usr \
	--with-regex=php \
	--with-snmp=shared,/usr \
	--with-sqlite3=shared,/usr \
	--with-tidy=shared,/usr \
	--with-unixODBC=shared,/usr \
	--with-vpx-dir=shared,/usr \
	--with-xmlrpc=shared \
	--with-xpm-dir=shared,/usr/X11R6 \
	--with-xsl=shared,/usr \
	--with-zlib \
	--with-zlib-dir=/usr \
	--without-gdbm \
	--without-mm \
	--without-t1lib \
	$(CONFIGURE_DTRACE_ARGS)

# MySQL configuration ( apply to PHP versions which don't support native MySQL driver )
MYSQL_CONGIG = \
	--with-mysql=shared,/usr \
	--with-mysqli=shared,/usr/bin/mysql_config \
	--with-pdo-mysql=shared,/usr

# MySQL native driver configuration ( apply only to PHP versions which support it )
MYSQLND_CONFIG = \
	--enable-mysqlnd=shared \
	--with-mysql=shared,mysqlnd \
	--with-mysqli=shared,mysqlnd \
	--with-pdo-mysql=shared,mysqlnd

# Compile both interbase and pdo_firebird extensions only if supported
ifneq ($(DEB_HOST_ARCH),$(filter $(DEB_HOST_ARCH),hurd-i386 m68k hppa ppc64))
  COMMON_CONFIG += --with-interbase=shared,/usr --with-pdo-firebird=shared,/usr
else
  COMMON_CONFIG += --without-interbase --without-pdo-firebird
endif

# OpenSSL-0.9.8 ( needed for php4.4 )

build-openssl-0.9.8: build-openssl-0.9.8-stamp
build-openssl-0.9.8-stamp:
	cd openssl-0.9.8* && ./config no-shared --prefix=$(PREFIX) && $(MAKE) all

	touch build-openssl-0.9.8-stamp

install-openssl-0.9.8: build-openssl-0.9.8 install-openssl-0.9.8-stamp
install-openssl-0.9.8-stamp:
	cd openssl-0.9.8* && $(MAKE) install

	touch install-openssl-0.9.8-stamp

prepared: prepared-stamp
prepared-stamp:
	./buildconf --force

	touch prepared-stamp

unprepared:
	rm -rf autom4te.cache
	rm -f buildmk.stamp prepared-stamp configure

configure-php4.4: install-openssl-0.9.8 configure-php4.4-stamp configure-common
configure-php4.4-stamp: prepared-stamp
	if [ -d cgi-build ]; then rm -rf cgi-build; fi
	-mkdir cgi-build
	cd cgi-build && \
	CFLAGS="$(CFLAGS)" PROG_SENDMAIL="$(PROG_SENDMAIL)" ../configure \
	$(COMMON_CONFIG) \
	$(MYSQL_CONGIG) \
	--disable-xml \
	--with-expat-dir=/usr \
	--enable-dbx \
	--enable-fastcgi \
	--enable-filepro \
	--enable-force-cgi-redirect \
	--enable-memory-limit \
	--enable-track-vars \
	--enable-trans-sid \
	--with-dom=shared,/usr \
	--with-dom-xslt=shared,/usr \
	--with-dom-exslt=shared,/usr \
	--with-mhash=shared,/usr \
	--with-mime-magic \
	--with-ncurses=/usr \
	--with-openssl=$(PREFIX) \
	--without-db4 \
	--without-libedit

	touch configure-php4.4-stamp

configure-php5.2: configure-php5.2-stamp configure-common
configure-php5.2-stamp: prepared-stamp
	if [ -d cgi-build ]; then rm -rf cgi-build; fi
	-mkdir cgi-build
	cd cgi-build && \
	CFLAGS="$(CFLAGS)" PROG_SENDMAIL="$(PROG_SENDMAIL)" ../configure \
		$(COMMON_CONFIG) \
		$(MYSQL_CONGIG) \
		--enable-memory-limit \
		--enable-force-cgi-redirect \
		--enable-fastcgi \
		--with-mhash=shared,/usr \
		--with-mime-magic \
		--with-sqlite=shared,/usr \
		--without-db4

	touch configure-php5.2-stamp

configure-php5.3: configure-php5.3-stamp configure-common
configure-php5.3-stamp: prepared-stamp
	if [ -d cgi-build ]; then rm -rf cgi-build; fi
	-mkdir cgi-build
	cd cgi-build && \
	CFLAGS="$(CFLAGS)" PROG_SENDMAIL="$(PROG_SENDMAIL)" ../configure \
		$(COMMON_CONFIG) \
		$(MYSQLND_CONFIG) \
		--enable-cgi \
		--with-sqlite=shared,/usr

	touch configure-php5.3-stamp

configure-php5.4: configure-php5.4-stamp configure-common
configure-php5.4-stamp: prepared-stamp
	if [ -d cgi-build ]; then rm -rf cgi-build; fi
	-mkdir cgi-build
	cd cgi-build && \
	CFLAGS="$(CFLAGS)" PROG_SENDMAIL="$(PROG_SENDMAIL)" ../configure \
		$(COMMON_CONFIG) \
		$(MYSQLND_CONFIG) \
		--enable-cgi \
		--with-sqlite=shared,/usr

	touch configure-php5.4-stamp

configure-php5.5: configure-php5.5-stamp configure-common
configure-php5.5-stamp: prepared-stamp
	if [ -d cgi-build ]; then rm -rf cgi-build; fi
	-mkdir cgi-build
	cd cgi-build && \
	CFLAGS="$(CFLAGS)" PROG_SENDMAIL="$(PROG_SENDMAIL)" ../configure \
		$(COMMON_CONFIG) \
		$(MYSQLND_CONFIG) \
		--enable-cgi \
		--with-sqlite=shared,/usr

	touch configure-php5.4-stamp

configure-php5.6: configure-php5.6-stamp configure-common
configure-php5.6-stamp: prepared-stamp
	if [ -d cgi-build ]; then rm -rf cgi-build; fi
	-mkdir cgi-build
	cd cgi-build && \
	CFLAGS="$(CFLAGS)" PROG_SENDMAIL="$(PROG_SENDMAIL)" ../configure \
		$(COMMON_CONFIG) \
		$(MYSQLND_CONFIG) \
		--enable-cgi

	touch configure-php5.6-stamp

configure-common: configure-common-stamp
configure-common-stamp:
	cd cgi-build && \
	cp ../Zend/zend_ini_scanner.c ../Zend/zend_language_scanner.c \
	   ../Zend/zend_ini_parser.h ../Zend/zend_language_parser.h \
	   ../Zend/zend_ini_parser.c ../Zend/zend_language_parser.c \
	   Zend/

	touch configure-common-stamp

build-php4.4: configure-php4.4 build-common
build-php5.2: configure-php5.2 build-common
build-php5.3: configure-php5.3 build-common
build-php5.4: configure-php5.4 build-common
build-php5.5: configure-php5.5 build-common
build-php5.6: configure-php5.6 build-common

build-common: build-common-stamp
build-common-stamp:
	cd cgi-build && $(MAKE)

	touch build-common-stamp

install-php4.4: build-php4.4 install-common
	ln -sf $(PREFIX)/bin/php $(PREFIX)/bin/php-cgi
	cp php.ini-phpswitcher $(TPLS_DESTDIR)/php4.4.ini

install-php5.2: build-php5.2 install-common
	cp php.ini-phpswitcher $(TPLS_DESTDIR)/php5.2.ini

install-php5.3: build-php5.3 install-common
	cp php.ini-phpswitcher $(TPLS_DESTDIR)/php5.3.ini

install-php5.4: build-php5.4 install-common
	cp php.ini-phpswitcher $(TPLS_DESTDIR)/php5.4.ini

install-php5.5: build-php5.5 install-common
	cp php.ini-phpswitcher $(TPLS_DESTDIR)/php5.5.ini

install-php5.6: build-php5.6 install-common
	cp php.ini-phpswitcher $(TPLS_DESTDIR)/php5.6.ini

install-common: install-common-stamp
install-common-stamp:
	cd cgi-build && $(MAKE) install

	mkdir -p $(PREFIX)/etc/php/conf.d

	if [ -f php.ini-recommended ]; then \
		cp php.ini-recommended $(PREFIX)/etc/php/php.ini; \
	else \
		cp php.ini-production $(PREFIX)/etc/php/php.ini; \
	fi

	cp modules.ini $(PREFIX)/etc/php/conf.d/modules.ini

	ln -sf /usr/share/misc/config.guess $(PREFIX)/lib/php/build/config.guess
	ln -sf /usr/share/misc/config.sub $(PREFIX)/lib/php/build/config.sub
	ln -sf /usr/share/aclocal/libtool.m4 $(PREFIX)/lib/php/build/libtool.m4
	ln -sf $(LTMAIN_DIR)ltmain.sh $(PREFIX)/lib/php/build/ltmain.sh
	ln -sf /usr/bin/shtool $(PREFIX)/lib/php/build/shtool

	touch install-common-stamp

clean: unprepared
	rm -f configure-common-stamp build-common-stamp install-common-stamp
	rm -f configure-php5.*-stamp
	rm -rf cgi-build

.NOTPARALLEL:
.PHONY: clean configure-php4.4 configure-php5.2 configure-php5.3 configure-php5.4 configure-php5.6 build-php4.4 build-php5.2 build-php5.3 build-php5.4 build-php5.6 install-php4.4 install-php5.2 install-php5.3 install-php5.4 install-php5.6 clean
