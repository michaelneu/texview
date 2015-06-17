"use strict";

/**
 * Initializes the IDE
 */
function TeXViewEditor(id, token, editor) {
	this.id     = id;
	this.token  = token;
	this.editor = editor;

	this.fileTree = $(".file-list");

	this.loadFileTree();
}

/**
 * Read the file tree and display it
 */
TeXViewEditor.prototype.loadFileTree = function() {
	var texview = this;

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
		file    = "<a href='#' class='file'>{0}</a>",
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
		tempElement.click(this.openDirectory);

		this.updateFileTree(folders[element], tempElement.find(".items"));
		currentElement.append(tempElement);
	}

	// add the files to the current element
	for (element in files) {
		if (files.hasOwnProperty(element) && /^0$|^[1-9]+$/.test(element)) {
			tempElement = $(file.replace("{0}", files[element]));
			tempElement.click(function (event) {
				texview.displayFile(this);
			});

			currentElement.append(tempElement);
		}
	}
};


TeXViewEditor.prototype.openDirectory = function(event) {
	var element = $(this),
		name    = "open";

	console.log(this);

	if (element.hasClass(name)) {
		element.removeClass(name);
	} else {
		element.addClass(name);
	}

	event.preventDefault();
	event.stopPropagation();

	return false;
};

TeXViewEditor.prototype.displayFile = function(element) {
	console.log(this);
};