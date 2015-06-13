<?php

# define the root path of the application
$root_path = "/var/www/htdocs/local/";


# if set to true it'll wipe the database (only wipes, if $root is defined too)
# use it with care as it'll delete all users and the project canonical names
$wipe_db = false;

# the root user credentials
# the password has to be plain text as it'll be hashed on the backend
# it is recommended to delete these credentials after setup
$root = array(
	"username" => "root",
	"password" => "your-super-secure-password"
);

?>