from compiler import *
import threading
import time
import logging

class Project: 
	"""Representation of a TeXView project"""
	def __init__(self, directory, compile_wait_time=5000): 
		"""Initialize a new representation of a project. The project will wait
		for compile_wait_time ms of no changes before compiling"""
		self.directory         = directory
		self.compiler          = Compiler(directory)
		self.compile_requests  = []
		self.already_compiling = False

		self.thread = ProjectObserver(self, compile_wait_time)
		self.thread.start()

	def queue_request(self):
		"""Queue a new compile request based on a change in the project"""
		logging.info("[REQUEST] Compile request for project \"%s\""%self.directory)
		self.compile_requests.append(time.time())

	def compile(self): 
		"""Compiles the project"""
		if not self.already_compiling: 
			self.already_compiling = True
			self.compile_requests = []

			self.compiler.compile()

			self.already_compiling = False
		else: 
			self.queue_request()


class ProjectObserver(threading.Thread):
	"""An observer which checks whether a compile of the project is required"""
	def __init__(self, project, wait_time): 
		threading.Thread.__init__(self)
		self.project   = project
		self.wait_time = wait_time

	def run(self):
		while True:
			requests = self.project.compile_requests

			if len(requests) > 0: 
				current_time = time.time()
				last_request = requests[-1]
				delta        = (current_time - last_request) * 1000

				if delta >= self.wait_time:
					self.project.compile()

			time.sleep(1)