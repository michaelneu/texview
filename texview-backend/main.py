#!/usr/bin/env python2

##############################################
### This is the main part of the texview-  ###
### backend. When active, it'll check for  ###
### file changes in the projects directory ###
### and compiles them to a new .pdf.       ###
##############################################

import logging
import texview
import texview.util.io

if __name__ == "__main__":
	logging.basicConfig(format="[%(asctime)s]   %(message)s", datefmt="%d.%m.%Y - %I:%M:%S", level=logging.INFO)
	logging.info("This is texview-backend. Starting watchdogs...")

	projects_directory = texview.util.io.get_projects_directory(__file__)

	watchdog = texview.Watchdog(projects_directory)
	watchdog.watch()
