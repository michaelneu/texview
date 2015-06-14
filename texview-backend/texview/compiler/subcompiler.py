from process import Process

class Subcompiler:
	"""A wrapper for running shell commands"""
	def __init__(self, compiler, main_file, flags=[]): 
		"""Initialize the object with the given parameters"""
		self.compiler  = compiler
		self.main_file = main_file
		self.flags     = flags

	def compile(self): 
		"""Run the compiler and return the stdout"""
		flags = [self.compiler] + self.flags + [self.main_file]

		process = Process(flags)
		stdout  = process.read_stdout()

		return stdout