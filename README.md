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
3.

## To do

* Write ```enrol_licensing``` to handle the course enrolment process
* Figure out how on earth program enrolment will work
* Write a bunch of targets for Totara organisations and audiences
* Write the UI
* Write the cron

## Thanks

A huge thank you to the wonderful folks at [Remploy](http://remploy.co.uk/), who
financed the development of this plugin.
