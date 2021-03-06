"use strict";

/**
 * Initializes the PDF preview handler
 *
 * @param {string} id The id of the project
 */
function TeXViewPDF(id) {
	this.id = id;

	this.previewContainer = $("#pdf-preview-container");
	this.previewFailedMsg = $("#not-found-message");
}

/**
 * Displays the "not found" message
 */
TeXViewPDF.prototype.showNotFoundMessage = function() {
	// hide the preview and show the message
	this.previewContainer.css("display", "none");
	this.previewFailedMsg.css("display", "table");
};


/**
 * Renders the pdf preview
 */
TeXViewPDF.prototype.showPreview = function() {
	var texviewpdf = this,
		scrolled = {
			x: window.scrollX,
			y: window.scrollY
		};

	// hide the error message and show the preview
	texviewpdf.previewContainer.css("display", "inherit");
	texviewpdf.previewFailedMsg.css("display", "none");
	
	// prepare rendering
	texviewpdf.previewContainer.empty();

	PDFJS.getDocument("../download/getpdf.php?project=" + texviewpdf.id).then(function (pdf) {
		var pages = pdf.numPages,
			i;

		for (i = 0; i < pages; i++) {
			pdf.getPage(i + 1).then(function (page) {
				texviewpdf.renderPage(page);

				// scroll back to the original position
				window.scrollTo(scrolled.x, scrolled.y);
			});
		}

	}).catch(function (error) {
		// ignore errors, just output them to the console
		console.log(error);
	});
};


/**
 * Renders and appends the page as <canvas> elements to #pdf-preview-container
 *
 * @param {PDFJS} page The PDFJS page
 */
TeXViewPDF.prototype.renderPage = function(page) {
	var canvas  = $("<canvas></canvas>"),
		previewWidth = this.previewContainer.width(),
		viewport = page.getViewport(1),
		context = canvas[0].getContext("2d");

	// calculate the viewport size
	viewport = page.getViewport(previewWidth / viewport.width);

	this.previewContainer.append(canvas);

	canvas.attr("width", viewport.width);
	canvas.attr("height", viewport.height);

	page.render({
		canvasContext: context,
		viewport: viewport
	});
};