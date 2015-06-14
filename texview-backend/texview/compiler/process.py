import subprocess

class Process(subprocess.Popen): 
	"""A subprocess wrapper to decrease code duplication"""
	def __init__(self, cmdline): 
		subprocess.Popen.__init__(self, cmdline, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

	def read_stdout(self): 
		"""Wait for the process to exit and return the stdout"""
		self.wait()

		return self.stdout.read()