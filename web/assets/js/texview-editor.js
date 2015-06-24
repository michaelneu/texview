"use strict";

/**
 * Initializes the IDE
 *
 * @param {string}      id           The project's id (from the URL)
 * @param {string}      token        The project's token (from the URL)
 * @param {CodeMirror}  editor       The CodeMirror instance of the editor
 * @param {DOM element} notification The element to display notifications in
 */
function TeXViewEditor(id, token, editor, notification) {
	var texview = this;

	// keep project related information
	texview.id    = id;
	texview.token = token;

	// instances of dom elements
	texview.fileTree = $(".file-list");
	texview.editor   = editor;

	// handle notifications
	texview.notificationShown = false;
	texview.notification      = $(notification);
	
	// flags for saving in general
	texview.current    = null;
	texview.savingFile = false;

	// flags for saving after modifying the text
	texview.saveWaitStarted = false;
	texview.saveWaitAgain   = false;
	texview.lastSavedText   = null;

	// register the change handler for codemirror
	texview.ignoreChange = false;
	editor.on("change", function (cm, change) {
		var text = cm.getValue();

		texview.onEditorChange(text);
	});

	// initially load the file tree
	texview.loadFileTree();
}


/**
 * Displays a notification toast
 *
 * @param {string} text The text to be displayed
 */
TeXViewEditor.prototype.notify = function(text) {
	var texview      = this,
		notification = texview.notification,
		message      = notification.find(".ui.message");

	if (!texview.notificationShown) {
		texview.notificationShown = true;

		message.text(text);
		notification.addClass("shown");

		setTimeout(function () {
			notification.removeClass("shown");
			texview.notificationShown = false;
		}, 3000);
	}
};


/**
 * Reads the file tree and displays it
 */
TeXViewEditor.prototype.loadFileTree = function() {
	var texview = this;

	texview.fileTree.empty();

	// no sanity check required as this is only executed if the session is valid
	$.ajax({
		url: "filetree.php?project=" + texview.id + "&token=" + texview.token,
		timeout: 30000,
		dataType: "json",
		success: function (data) {
			texview.updateFileTree(data);
		},
		error: function (data) {
			console.log("Error loading file tree. Retrying...");

			setTimeout(function () {
				texview.loadFileTree();
			}, 1000);
		}
	});
};

/**
 * Displays the json data retrieved from `loadFileTree`. 
 *
 * @param {json}        data          The retrieved data
 * @param {DOM element} parentElement The parent element of the current object. Optional. 
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
		currentElement = texview.fileTree;
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

		texview.updateFileTree(folders[element], tempElement.find(".items"));
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

	// notify about initialisation
	texview.notify("Initilisation finished");
};


/**
 * Opens a directory in the file list
 *
 * @param  {DOM element} element The selected element
 * @return {bool}                Prevents the default action for clicking
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
 *
 * @param {DOM element} element The selected file
 * @param {string}      path    The file's path
 */
TeXViewEditor.prototype.displayFile = function(element, path) {
	var encodedPath = encodeURIComponent(path),
		texview     = this;

	element = $(element);

	if (!texview.savingFile && !texview.saveWaitStarted) {
		// disable the current selection 
		$(".file-list .selected").removeClass("selected");

		// start loading the file
		$.ajax({
			url: "file.php?project=" + texview.id + "&token=" + texview.token + "&file=" + encodedPath,
			timeout: 3000,
			success: function (data) {
				texview.ignoreChange = true;

				texview.current = path;
				texview.editor.setValue(data);
				texview.editor.focus();
				texview.editor.clearHistory();

				element.addClass("selected");

				texview.ignoreChange = false;
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
 * Handles saving the file after a change made in the editor
 *
 * @param {string} text The current text of the editor
 */
TeXViewEditor.prototype.onEditorChange = function(text) {
	var texview = this;

	if (!texview.ignoreChange && texview.lastSavedText != text) {
		if (!texview.saveWaitStarted) {
			texview.saveWaitStarted = true;
			texview.saveWaitAgain   = true;

			texview.waitForChanges();
		} else {
			texview.saveWaitAgain = true;
		}
	}
};

/**
 * Waits for changes in the editor. 
 */
TeXViewEditor.prototype.waitForChanges = function() {
	var texview = this;

	if (texview.saveWaitAgain) {
		texview.saveWaitAgain = false;

		setTimeout(function () {
			texview.waitForChanges();
		}, 750);
	} else {
		texview.savingFile      = true;
		texview.saveWaitStarted = false;

		texview.saveFile();
	}
};

/**
 * Send the file to the server
 */
TeXViewEditor.prototype.saveFile = function() {
	var texview = this,
		text    = texview.editor.getValue(),
		json    = {
			"project": texview.id,
			"token": texview.token,
			"file": texview.current,
			"content": text
		};

	texview.lastSavedText = text;

	$.ajax({
		type: "POST",
		url: "save.php",
		data: json,
		success: function (data) {
			texview.savingFile = false;

			if (data != "") {
				texview.notify("Error loading file: <br>" + data);
			} else {
				texview.notify("File saved");
			}
		}, 
		error: function (data) {
			console.log("Error saving file. Retrying in 1s");

			setTimeout(function () {
				texview.saveFile();
			}, 1000);
		}
	});
};