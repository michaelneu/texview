<?php
require_once("../../php/administration/user.php");
require_once("../../php/security/bouncer.php");

?>
<!doctype html>
<html>
	<head>
		<title>Settings &middot; TeXView</title>
		
		<link rel="stylesheet" href="../assets/libs/Semantic-UI/dist/semantic.min.css" />
		<link rel="stylesheet" href="../assets/css/semantic-fixes.css" />
		<link rel="stylesheet" href="../assets/css/dashboard.css" />
	</head>

	<body>
		<!-- <navbar> -->
		<div class="ui green inverted fixed menu">
			<div class="container">
				<a class="item" href="../dash/"><i class="align left icon"></i> Dashboard</a>
				<a class="active item" href="index.php"><i class="align left icon"></i> Settings</a>

				<div class="right menu">
					<a class="item" href="?logout"><i class="share icon"></i> Logout</a>
				</div>
			</div>
		</div>
		<!-- </navbar> -->

		<!-- <main container> -->
		<div class="container">

			<!-- <password settings> -->
			<div class="ui segment">
				<h2>Password</h2>

				<form method="post" action="update-password.php" class="ui form" id="password-form">
					<div class="field">
						<input type="password" name="new-password" placeholder="New password">
					</div>

					<div class="field">
						<input type="password" name="validate-password" placeholder="Validate the password">
					</div>

					<div class="right-aligned field">
						<input type="submit" class="ui green button" value="Update password">
					</div>
					<div class="prompt"></div>
				</form>
			</div>
			<!-- <password settings> -->
<?php

if (User::is_root()) {
	echo "
			<!-- <root user management> -->
			<div class=\"ui grid\">
				<div class=\"row\">

					<!-- <manage all users> -->
					<div class=\"ten wide column\">
						<div class=\"ui segment\">
							<h2>User management</h2>

							<table class=\"ui table\">
								<thead>
									<th>Username</th>
									<th></th>
									<th></th>
								</thead>
								<tbody>";

	$users = User::get_all_users();

	foreach ($users as $row) {
		$id   = $row["id"];
		$name = $row["username"];

		echo "
									<tr>
										<td>$name</td>
										<td>
											<a href=\"#\" data-user=\"$id\">Reset password</a>
										</td>
										<td>
											<a href=\"deluser.php?user=$id\">
												<i class=\"ui red trash icon\"></i>
											</a>
										</td>
									</tr>";
	}

	echo "
								</tbody>
							</table>";

	if (count($users) == 0) {
		echo "
						<div class=\"center-aligned text\">
							No users created yet
						</div>";
	}

	echo "
						</div>
					</div>
					<!-- </manage all users> -->

					<!-- <add new user> -->
					<div class=\"six wide column\">
						<div class=\"ui segment\">
							<h2>Create new user</h2>

							<form method=\"post\" action=\"adduser.php\" class=\"ui form\">
								<div class=\"field\">
									<input type=\"text\" name=\"username\" placeholder=\"Username\" required>
								</div>

								<div class=\"field\">
									<button type=\"submit\" class=\"ui right labeled green fluid icon button\">
										<i class=\"plus icon\"></i>
										Create
									</button>
								</div>
							</form>
						</div>
					</div>
					<!-- </add new user> -->

				</div>
			</div>
			<!-- </root user management> -->";
}

?>
		</div>
		<!-- </main container> -->

		<script type="text/javascript" src="../assets/libs/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="../assets/libs/Semantic-UI/dist/semantic.min.js"></script>
		<script type="text/javascript" src="../assets/js/texview-settings.js"></script>
		<script type="text/javascript">
			$(function () {
				$("#password-form").form({
					newPassword: {
						identifier: "new-password",
						rules: [{
							type: "empty",
							prompt: "Please enter a password"
						}]
					},
					validatePassword: {
						identifier: "validate-password",
						rules: [{
							type: "empty",
							prompt: "Please enter a password"
						}, {
							type: "match[new-password]",
							prompt: "Passwords don't match"
						}]
					}
				});

				var settings = new TeXViewSettings();
				settings.initialize();
			});
		</script>
	</body>
</html>