## Mathhub.info
This project is the Drupal implementation of the MathHub system 
available at [MathHub.info](http://mathhub.info). 
It is based on the [planetary system](https://github.com/KWARC/planetary).

## Dependencies
Other than the usual Drupal/Planetary dependencies a MathHub installation requires: 
* A running MMT instance
* CleanURL configured and enabled (for MMT-like URIs)
* the lmh tool (see [here](http://mathhub.info/help/lmh))
* Archives to be served configured in the interface (see configuration below)
* (Optionally) a [MathWebSearch](http://search.mathweb.org) instance running

## Configuration
### Standard Directory Structure 
MathHub and its dependencies can be installed anywhere on the filesystem. However, this documentation
assumes a standard layout which is documented below. _If your layout is different, adjust the setup accordingly_
* MathHub Installation `/var/www/planetary/`
* LMH Installation `/var/data/localmh/` (see below how to set this)
* Apache Installation `/etc/apache2/`
* (Optionally) MathHub Mailing Script at `/var/data/mailer`

### Apache Configuration
* Enable rewriting (for CleanURL's) -- might have to enable the rewriting module in apache first:
```
<Directory /var/www/planetary/>
  RewriteEngine on
  RewriteBase /
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]
</Directory>
```
* Define Apache virtual host:
```
<VirtualHost *:80>
 ServerName planetary
 DocumentRoot /var/www/planetary/
</VirtualHost>
```
* (Optionally) If your www-data user has a .ssh folder for access to the repository (e.g. for `lmh update` and `git pull/push`), make sure the folder is not 
publicly accessible. (There are other ways to do this). 
```
<Directory /var/www/.ssh/>
	Options FollowSymLinks
	AllowOverride None
	Require all denied
</Directory>
```

### Lmh configuration
* To set the default lmh path for a user create a `.lmhpath` file in that users home containing the path (e.g. `/var/data/localmh`)
* the `.lmhpath` file must be set (also) for the www-data user 
* The `www-data` user must have read/write permissions on the lmh repositories folder ('/var/data/localmh/MathHub'). This can be achieved by changing owner or group and adjusting permissions accordingly.

### MathHub Configuration
* Enable the modules OAFF Base, MMT Integration, LaTeXML Integration, JOBAD and (optionally) MathHub Mailer
* In MMT Configuration (admin/config) set the URL of the local MMT instance
* In the Repositories Configuration (admin/config) set the location of the lmh repositories (`/var/data/localmh`). The kind of repository should be git for automatic pull/push from repositories (slower), and local otherwise (recommended).
* In the Text Formats configuration (admin/config) add corresponding formats for the archive you want to serve, and enable the relevant filters for them. 
e.g. An sTeX text format should have following filters enabled (in this processing order)
    1. Local LateXML Compilation filter -- LaTeXML profile should be `stex-module`
    2. MMT Compilation filter -- Input Format should be `stex`
    3. MMT Presentation filter -- Presentation style should be `planetary`
* In the OAFF Configuration (admin/config) set up the archives to be served
    1. Set the text format associated with each archive as key-value pairs of an `archive id` and a `text format` (defined at the previous step).
    Key and value are separated by one space, each pair by a newline. e.g.
    ```
    smglom/mv stex
    MMT/urtheories mmt
    MMT/examples mmt
    ```
    2. Set the content type associated with each text format as key-value pair of `text format` and `content type`. MathHub modules defined their own content types, 
    e.g. OAFF Base defines 'oaff_doc' for generic OAFF documents, MMT defines `mmtdoc` for more specific MMT documents. External modules may define more specialized
    content types. The syntax is as above (spaces/newlines as separators). e.g.
    ```
    stex oaff_doc
    mmt mmtdoc
    ```
    3. Set the file extension associated with each format as a key-value pair of `text format` and `extension`. This is used for crawling the archives on the filesystem to automatically creates corresponding nodes in MathHub. The syntax is as above (spaces/newlines as separators). e.g.
    ```
    stex tex
    mmt mmt
    ```
    4. Choose whether to enable logging. If logging is enabled admin users will see a foldable (by default folded) developer log at the bottom of every page. It 
    shows what happened internally to produce the page and includes logs of external programs (git, LaTeXML, lmh), filters run, etc.

### MathHub Administration
//TODO
