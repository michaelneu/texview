"use strict";

/**
 * Initialize the TeXViewReader object. 
 */
function TeXViewReader(id) {
	this.id      = id;
	this.preview = new TeXViewPDF(id);

	// get the current state of the PDF without waiting the $time_limit (short poll)
	this.poll(true);
}


/**
 * Long poll for changes in the PDF
 * 
 * @param {boolean} force Force the update
 */
TeXViewReader.prototype.poll = function(force) {
	var texview = this;
	
	force = force !== undefined ? "&force" : "";

	if (texview.id !== undefined) {
		$.ajax({
			url: "status.php?project=" + texview.id + force,
			timeout: 30000,
			dataType: "json",
			success: function (data) {
				texview.onPollSuccess(data);
			},
			error: function (data) {
				texview.onPollFailed(data);
			}
		});
	}
};


/**
 * Changes the element's style if the current style is different to the new style
 * 
 * @param {jQuery} element      The element to change
 * @param {string} currentStyle The current style of the element
 * @param {string} newStyle     The new style of the element
 */
TeXViewReader.prototype.__changeStyleIfRequired = function(element, currentStyle, newStyle) {
	if (currentStyle != newStyle) {
		element.removeClass();
		element.addClass(newStyle);
	}
};

/**
 * Color the navbar according to the PDF's compile state
 *
 * @param {string} state The compile state of the PDF
 */
TeXViewReader.prototype.__colorNavbar = function(state) {
	var STYLE_ERROR = "ui red inverted animated fixed menu",
		STYLE_SUCCESS = "ui green inverted animated fixed menu",
		STYLE_UNKNOWN = "ui yellow inverted animated fixed menu",
		menuBar = $(".ui.menu"),
		currentClass = menuBar.attr("class");


	switch (state) {
		case "FAIL":
			this.__changeStyleIfRequired(menuBar, currentClass, STYLE_ERROR);
			break;

		case "SUCCESS":
			this.__changeStyleIfRequired(menuBar, currentClass, STYLE_SUCCESS);
			break;

		default:
			this.__changeStyleIfRequired(menuBar, currentClass, STYLE_UNKNOWN);
			break;
	}
};

/**
 * Checks if the preview should be updated. Called by TeXViewReader.poll()
 *
 * @param {json} data The polling result
 */
TeXViewReader.prototype.onPollSuccess = function(data) {
	var status = data.status;

	this.__colorNavbar(status);

	if (data.reloadRequired) {
		if (status == "SUCCESS") {
			this.preview.showPreview();
		} else {
			this.preview.showNotFoundMessage();
		}
	}

	this.poll();
};

/**
 * Restarts the polling if it failed after 500ms. Called by TeXViewReader.poll()
 *
 * @param {json} data The errored XHR data
 */
TeXViewReader.prototype.onPollFailed = function(data) {
	var texview = this;

	texview.__colorNavbar("FAIL");

	setTimeout(function () {
		texview.poll();
	}, 500);
};
