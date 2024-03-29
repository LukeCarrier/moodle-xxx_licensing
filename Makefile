#
# Moodle licensing enrolment plugin.
#
# @author Luke Carrier <luke@carrier.im>
# @author Luke Carrier <luke@tdm.co>
# @copyright 2014 Luke Carrier, The Development Manager Ltd
#

.PHONY: all clean

TOP                   := $(dir $(CURDIR)/$(word $(words $(MAKEFILE_LIST)), $(MAKEFILE_LIST)))
NPM_BIN               := $(TOP)node_modules/.bin/
BUILD_LOCAL_LICENSING := $(TOP)build/local_licensing/

all: build/local_licensing.zip

build/local_licensing.zip:
	mkdir -p $(BUILD_LOCAL_LICENSING)
	cp -rv $(TOP)src-local_licensing $(BUILD_LOCAL_LICENSING)licensing
	cp $(TOP)README.md $(BUILD_LOCAL_LICENSING)licensing/README.txt
	$(TOP)node_modules/mustache-wax/lib/templates/compile.sh \
		$(TOP)lib/mustache.template.js
	mv $(TOP)lib/mustache.js $(BUILD_LOCAL_LICENSING)mustache.js
	cd $(TOP)build/local_licensing/licensing  \
		&& $(NPM_BIN)wax \
			-f yui/src/chooserdialogue/js/templates.js \
			-n Moodle.local_licensing.chooserdialogue \
			-p moodle-local_licensing-chooserdialogue \
			-t $(BUILD_LOCAL_LICENSING)mustache.js \
			-b -v handlebars/chooserdialogue \
		&& $(NPM_BIN)wax \
			-f yui/src/userchooserdialogue/js/templates.js \
			-n Moodle.local_licensing.userchooserdialogue \
			-p moodle-local_licensing-userchooserdialogue \
			-t $(BUILD_LOCAL_LICENSING)mustache.js \
			-b -v handlebars/userchooserdialogue
	cd $(TOP)build/local_licensing/licensing/yui/src \
		&& $(NPM_BIN)shifter --walk
	cd $(BUILD_LOCAL_LICENSING)licensing \
		&& rm -rfv handlebars
	cd $(BUILD_LOCAL_LICENSING) \
		&& zip -r ../local_licensing.zip licensing
	rm -rfv $(BUILD_LOCAL_LICENSING)

clean:
	rm -rf $(TOP)build
