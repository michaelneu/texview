from makeindex import *
from pdflatex import *

import os
import os.path
import shutil
import logging

class Compiler: 
	"""A wrapper for pdflatex"""
	def __init__(self, directory, main_file="main"): 
		"""Initializes a new Compiler which compiles directory/main_file.tex 
		and returns the output of pdflatex"""
		self.main_file   = main_file + ".tex"
		self.index_file  = main_file + ".idx"

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
		logging.info("[COMPILE] Compile of project \"%s\" started"%self.directory)

		# clean the build directory
		self.clean_build()
		self.prepare_compile()

		# compile with pdflatex
		pdflatex        = Pdflatex(self.main_file)
		pdflatex_stdout = pdflatex.compile()

		# check if a second compile is neccessary
		compile_result = CompilerResult(pdflatex_stdout)
		result = compile_result.get_result()
		logging.info("[COMPILE][PDFLATEX] Compile of project \"%s\" finished. Result was %s"%(self.directory, result))

		if result == CompilerResult.SUCCESS: 
			# prepare the latex index file
			if os.path.exists(self.index_file): 
				makeindex = Makeindex(self.index_file)
				makeindex.compile()
				logging.info("[COMPILE][MAKEINDEX] Index processed for project \"%s\""%self.directory)

			# compile second time with pdflatex
			pdflatex_stdout = pdflatex.compile()

			# analyze the output of the second compile pass
			compile_result = CompilerResult(pdflatex_stdout)
			logging.info("[COMPILE][PDFLATEX] Compile of project \"%s\" finished. Result was %s"%(self.directory, compile_result.get_result()))

		return compile_result


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
