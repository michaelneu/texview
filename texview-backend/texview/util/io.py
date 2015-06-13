import os
import os.path

def get_parent_directory(path): 
	"""Returns the parent directory of the given path"""
	parent_directory = os.path.join(path, os.pardir)
	parent_directory = os.path.abspath(parent_directory)

	return parent_directory

def get_projects_directory(current_file):
	"""Returns the directory which contains the projects"""
	current_file = os.path.abspath(current_file)
	current_directory = os.path.dirname(current_file)

	parent_directory = get_parent_directory(current_directory)
	projects_directory = os.path.join(parent_directory, "projects")

	return projects_directory
