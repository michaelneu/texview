from eventhandler import *
from watchdog.observers import Observer

import time
import logging
import sys

class Watchdog: 
	"""A watchdog which keeps track of changes in the projects directory"""
	def __init__(self, directory): 
		self.directory = directory

		self.change_handler = ChangeHandler(self.directory)
		self.observer = Observer()
		self.observer.schedule(self.change_handler, self.directory, recursive=True)

		logging.info("Watchdog on \"%s\" started"%self.directory)

	def watch(self): 
		"""Start observing the directory"""
		self.observer.start()

		try: 
			while True: 
				time.sleep(1)
		except KeyboardInterrupt: 
			logging.info("Received KeyboardInterrupt, shutting down")
			self.observer.stop()


		self.observer.join()

		# kill all threads
		sys.exit(0)