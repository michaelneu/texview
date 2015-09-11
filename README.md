# texview

A web based LaTeX build-server


### Installation

#### Prerequisites

Before TeXView will work you'll need the following packages from your distro's repositories (here's the Debian ones): 

- texlive-full (includes all CTAN packages, so you won't have to download them manually)
- php5-fpm
- python2
- nginx
- screen (optional, but recommended)

Make sure nginx/php is configured to run `.php` files and that texlive is up-to-date as well. Now go ahead and clone the repository to your webserver's directory. 


#### Configuration

Adjust the settings in the `config.php` file to suit your needs, watch out to specify the `$root_path` variable as this is used globally. It's recommended to remove the `$root` definition as this should stay "secret". 


#### Running

The automated builds require `texview-backend/main.py` to be running in the background. 
```
$ cd texview/
$ screen -S texview -d -m texview-backend/main.py
```
Now, that the backend is running, you can open your browser and visit the url you configured in nginx. After you signed in with the root credentials (from `config.php`) you can create projects and users. The backend will try to compile your project as soon as there's a change in the file tree. 


### License

TeXView is released under the [GPLv3](LICENSE.txt)
