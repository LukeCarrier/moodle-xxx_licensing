#
# Moodle licensing enrolment plugin.
#
# @author Luke Carrier <luke@carrier.im>
# @author Luke Carrier <luke@tdm.co>
# @copyright 2014 Luke Carrier, The Development Manager Ltd
#

.PHONY: all clean

TOP := $(dir $(CURDIR)/$(word $(words $(MAKEFILE_LIST)), $(MAKEFILE_LIST)))

all: build/local_licensing.zip

build/local_licensing.zip:
	mkdir -p $(TOP)build/local_licensing
	cp -rv $(TOP)src-local_licensing $(TOP)build/local_licensing/licensing
	cp $(TOP)README.md $(TOP)build/local_licensing/licensing/README.txt
	cd $(TOP)build/local_licensing/licensing \
		&& rm -rfv handlebars
	cd $(TOP)build/local_licensing \
		&& zip -r ../local_licensing.zip licensing
	rm -rfv $(TOP)build/local_licensing

clean:
	rm -rf $(TOP)build
