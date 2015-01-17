# License enrolment plugin for Moodle and Totara LMS

This plugin extends Moodle with the ability to manage enrolments in the form of
per-user licenses.

## Key terms

In addition the ordinary Moodle lingo, you'll need to be aware of the following
concepts in order to understand the functionality provided by this plugin:

* A license is redeemable as a single enrolment for a single user in a single course

## The process

There are a few steps in between a license being issued and the user being
enrolled within the specified course.

## Installing

## Building

If you're planning on installing the licensing system and don't want to make any
changes, I'd recommend skipping this step in favour of installing from the
distributions made available on Moodle.org.

1. Clone the source.
2. ```cd``` into it.
3. Install the build dependencies with ```npm install```.
4. Build the plugins with ```make```.
5. Each of the plugins will now be packaged into zip files under the
   ```build/``` directory.

## To do

* Figure out how on earth program enrolment will work
* Write a bunch of targets for Totara organisations and audiences
* Write the UI
* Write the cron
* Move all the selectors and other magic values in the client-side product
  dialogue code into module attributes for cleanliness and recognition
* Display course icons in the product dialogue for Totara users
* Display Totara organisations in a tree view rather than just a list

## Thanks

A huge thank you to the wonderful folks at [Remploy](http://remploy.co.uk/), who
financed the development of this plugin.
