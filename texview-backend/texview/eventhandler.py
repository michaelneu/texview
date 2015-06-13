from project import *

from watchdog.events import FileSystemEventHandler

import os
import os.path

class ChangeHandler(FileSystemEventHandler):
	"""Handles the changes made to the filesystem"""
	def __init__(self, directory):
		"""Initialize the change handler with the directory to be observed. """
		FileSystemEventHandler.__init__(self)

		self.directory = directory
		self.projects = {}

	def on_any_event(self, event): 
		"""Decides whether a new compile should be started"""
		event_type = event.event_type

		target = event.src_path.lstrip(self.directory)
		target_project = target.split(os.sep)[0]

		# filter the changes to the project's base directory as we're only
		# interested in changes within the project
		if not (event_type == "modified" and target == target_project): 
			if target_project not in self.projects.keys():
				project_path = os.path.join(self.directory, target_project)
				self.projects[target_project] = Project(project_path)

			self.projects[target_project].queue_request()

