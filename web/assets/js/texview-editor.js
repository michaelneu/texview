"use strict";

/**
 * Initializes the IDE
 */
function TeXViewEditor(id, token, editor) {
	var texview = this;

	texview.id    = id;
	texview.token = token;

	texview.fileTree = $(".file-list");
	texview.editor   = editor;
	
	texview.current    = null;
	texview.savingFile = false;


	// register the change handler for codemirror
	editor.on("change", function (cm, change) {
		var text = cm.getValue();

		texview.onEditorChange(text);
	});

	texview.loadFileTree();
}


/**
 * Reads the file tree and displays it
 */
TeXViewEditor.prototype.loadFileTree = function() {
	var texview = this;

	texview.fileTree.empty();

	// no sanity check required as this is only executed if the session is valid
	$.ajax({
		url: "filetree.php?project=" + this.id + "&token=" + this.token,
		timeout: 30000,
		dataType: "json",
		success: function (data) {
			texview.updateFileTree(data);
		},
		error: function (data) {
			setTimeout(function () {
				texview.loadFileTree();
			}, 1000);
		}
	});
};

/**
 * Displays the json data retrieved from `loadFileTree`. 
 */
TeXViewEditor.prototype.updateFileTree = function(data, parentElement) {
	var texview = this,
		folders = data.folders,
		files   = $(data.files),
		folder  = "<div class='folder'><div class='text'>{0}</div><div class='items'></div></div>",
		file    = "<div class='file'>{0}</div>",
		currentElement, tempElement, element;

	// if there's no parent element given, assume we're root
	if (parentElement === undefined) {
		currentElement = this.fileTree;
	} else {
		currentElement = parentElement;
	}

	// add the folders to the current element
	for (element in folders) {
		tempElement = $(folder.replace("{0}", element));
		tempElement.click(function (event) {
			texview.openDirectory(this);

			event.preventDefault();
			event.stopPropagation();

			return false;
		});

		this.updateFileTree(folders[element], tempElement.find(".items"));
		currentElement.append(tempElement);
	}

	// add the files to the current element
	for (element in files) {
		if (files.hasOwnProperty(element) && /^0$|^[1-9]+$/.test(element)) {
			// create closure for using the basename and path from the array
			(function (basename, path) {
				// create element, assign the click handler and append it
				tempElement = $(file.replace("{0}", basename));
				tempElement.click(function (event) {
					texview.displayFile(this, path);

					event.preventDefault();
					event.stopPropagation();

					return false;
				});

				currentElement.append(tempElement);
			})(files[element].basename, files[element].path);
		}
	}
};


/**
 * Opens a directory in the file list
 */
TeXViewEditor.prototype.openDirectory = function(element) {
	var element = $(element),
		name    = "open";

	if (element.hasClass(name)) {
		element.removeClass(name);
	} else {
		element.addClass(name);
	}

	event.preventDefault();
	event.stopPropagation();

	return false;
};

/**
 * Loads the selected file in the editor
 */
TeXViewEditor.prototype.displayFile = function(element, path) {
	var encodedPath = encodeURIComponent(path),
		texview     = this;

	element = $(element);

	if (!texview.savingFile) {
		// disable the current selection 
		$(".file-list .selected").removeClass("selected");

		// start loading the file
		$.ajax({
			url: "file.php?project=" + texview.id + "&token=" + texview.token + "&file=" + encodedPath,
			timeout: 3000,
			success: function (data) {
				texview.current = path;
				texview.editor.setValue(data);
				texview.editor.focus();

				element.addClass("selected");
			},
			error: function (data) {
				alert("Error loading file");
			}
		});
	} else {
		alert("Please wait for the file to be saved");
	}
};

/**
 * 
 */


/**
 * Handles saving the file after a change made in the editor
 */
TeXViewEditor.prototype.onEditorChange = function(text) {

};