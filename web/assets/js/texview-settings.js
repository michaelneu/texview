"use strict";

/**
 * Creates a new instance of the settings API
 */
function TeXViewSettings () {

}

/**
 * Initializes the forms
 */
TeXViewSettings.prototype.initialize = function() {
	$("a[data-user]").click(this.resetPassword);
};

/**
 * Resets the user's password
 * 
 * @param {event} event The click event of the "Reset password" link
 */
TeXViewSettings.prototype.resetPassword = function(event) {
	var element = $(this),
		id      = element.attr("data-user"); 



	$.ajax({
		url: "update-password.php?user=" + id,
		dataType: "json",
		success: function (data) {
			var parent = element.parent();

			parent.html("The new password is <br><b>" + data.password + "</b>");
		},
		error: function (data) {
			alert("Error while resetting password. \nPlease try again");
		}
	});
};