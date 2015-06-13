import subprocess
import os
import os.path
import shutil

class Compiler: 
	"""A wrapper for pdflatex"""
	def __init__(self, directory, main_file="main.tex", extra_flags=["--halt-on-error", "--shell-escape"]): 
		"""Initializes a new Compiler which compiles directory/main_file.tex 
		and returns the output of pdflatex"""
		self.extra_flags = extra_flags
		self.main_file   = main_file

		self.directory       = directory
		self.src_directory   = os.path.join(self.directory, "src")
		self.build_directory = os.path.join(self.directory, "build")

	def clean_build(self): 
		"""Clear the build directory"""
		shutil.rmtree(self.build_directory)
		os.mkdir(self.build_directory)

	def prepare_compile(self): 
		"""Copy the files from the src directory to the build directory"""
		for item in os.listdir(self.src_directory): 
			src_item   = os.path.join(self.src_directory, item)
			build_item = os.path.join(self.build_directory, item)

			if os.path.isdir(src_item): 
				shutil.copytree(src_item, build_item)
			else: 
				shutil.copy2(src_item, build_item)

		os.chdir(self.build_directory)

	def compile(self): 
		"""Runs pdflatex and returns a CompilerResult object"""
		self.clean_build()
		self.prepare_compile()

		start_info = ["pdflatex"] + self.extra_flags + [self.main_file]

		process = subprocess.Popen(start_info, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
		process.wait()

		stdout = process.stdout.read()

		return CompilerResult(stdout)


class CompilerResult: 
	"""An information structure about the compile result"""
	SUCCESS = "SUCCESS"
	FAIL    = "FAIL"

	def __init__(self, stdout): 
		"""Initializes the information using the compiler's stdout"""
		self.stdout = stdout

	def get_result(self): 
		"""Returns the state of the last compile"""
		if "\n!" in self.stdout: 
			return CompilerResult.FAIL
		else: 
			return CompilerResult.SUCCESS
