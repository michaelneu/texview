from subcompiler import Subcompiler

class Pdflatex(Subcompiler):
	"""A wrapper for the pdflatex command"""
	def __init__(self, main_file, flags=["--halt-on-error", "--shell-escape"]): 
		Subcompiler.__init__(self, "pdflatex", main_file, flags)