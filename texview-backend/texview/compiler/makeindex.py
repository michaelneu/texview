from subcompiler import Subcompiler

class Makeindex(Subcompiler):
	"""A wrapper for the pdflatex command"""
	def __init__(self, main_file, flags=[]): 
		Subcompiler.__init__(self, "makeindex", main_file, flags)