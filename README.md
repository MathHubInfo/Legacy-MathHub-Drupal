# MathHub.info [![Build Status](https://secure.travis-ci.org/KWARC/MathHub.png?branch=master)](http://travis-ci.org/KWARC/MathHub) [![Stories in Ready](https://badge.waffle.io/kwarc/mathhub.png?label=ready&title=Ready)](https://waffle.io/kwarc/mathhub)
This project is the Drupal implementation of the MathHub system 
available at [MathHub.info](http://mathhub.info). 
It is based on the [planetary system](https://github.com/KWARC/planetary).

## Dependencies
Other than the usual Drupal/Planetary dependencies a MathHub installation requires: 
* A running [MMT](https://svn.kwarc.info/repos/MMT/doc/html/index.html) instance
* [CleanURL](https://drupal.org/getting-started/clean-urls) configured and enabled (for MMT-like URIs)
* the [lmh](http://mathhub.info/help/lmh) tool
* Archives to be served configured in the interface (see configuration below)
* (Optionally) a [MathWebSearch](http://search.mathweb.org) instance running

## Configuration
### Standard Directory Structure 
MathHub and its dependencies can be installed anywhere on the filesystem. However, this documentation
assumes a standard layout which is documented below. _If your layout is different, adjust the setup accordingly_
* MathHub Installation `/var/www/planetary/`
* lmh Installation `/var/data/localmh/` (see below how to set this)
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
 ServerName MathHub
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

### lmh configuration
* To set the default lmh path for a user create a `.lmhpath` file in that users home containing the path (e.g. `/var/data/localmh`)
* the `.lmhpath` file must be set (also) for the www-data user 
* The `www-data` user must have read/write permissions on the lmh repositories folder (`/var/data/localmh/MathHub`). This can be achieved by changing owner or group and adjusting permissions accordingly.

### MathHub Configuration
* Enable the modules OAFF Base, MMT Integration, LaTeXML Integration, JOBAD and (optionally) MathHub Mailer
* In MMT Configuration (admin/config) set the URL of the local MMT instance
* In the Repositories Configuration (admin/config) set the location of the lmh repositories (`/var/data/localmh`). The kind of repository should be git for automatic pull/push from repositories (slower), and local otherwise (recommended).
* In the Text Formats configuration (admin/config) add corresponding formats for the archives you want to serve, and enable the relevant filters for them. 
e.g. An sTeX text format should have following filters enabled (in this processing order)
    1. Local LateXML Compilation filter -- LaTeXML profile should be `stex-module`
    2. MMT Compilation filter -- Input Format should be `stex`
    3. MMT Presentation filter -- Presentation style should be `planetary`
* In the OAFF Configuration (admin/config) set up the archives to be served
    * Set the text format associated with each archive as key-value pairs of an `archive id` and a `text format` (defined at the previous step).
    Key and value are separated by one space, each pair by a newline. e.g.
    ```
    smglom/mv stex
    MMT/urtheories mmt
    MMT/examples mmt
    ```
    * Set the content type associated with each text format as key-value pair of `text format` and `content type`. MathHub modules defined their own content types, 
    e.g. OAFF Base defines `oaff_doc` for generic OAFF documents, MMT defines `mmtdoc` for more specific MMT documents. External modules may define more specialized
    content types. The syntax is as above (spaces/newlines as separators). e.g.
    ```
    stex oaff_doc
    mmt mmtdoc
    ```
    * Set the file extension associated with each format as a key-value pair of `text format` and `extension`. This is used for crawling the archives on the filesystem to automatically creates corresponding nodes in MathHub. The syntax is as above (spaces/newlines as separators). e.g.
    ```
    stex tex
    mmt mmt
    ```
    * Choose whether to enable logging. If logging is enabled admin users will see a foldable (by default folded) developer log at the bottom of every page. It 
    shows what happened internally to produce the page and includes logs of external programs (git, LaTeXML, lmh), filters run, etc.

## Administration
Administering MathHub (after installation and configuration) mostly involves keeping this up to date and getting an overview of its state.
Currently MathHub Admins have access to the following functionalities (via links in the Navigation menu) : 
* Initialize nodes -- crawls the filesystem and creates corresponding nodes for each relevant file (right extension) in enabled archives (see OAFF Configuration above to read about enabling and configuring archives). Checks if nodes already exists so can be re-run safely, will create new nodes only if new files were added to the filesystem (e.g. after an Lmh Update or git pull). Creates a maximum of 1000 nodes at a time, should be re-run until finished if needed.
* Crawl loaded nodes -- crawls loaded nodes and tries to render them (runs all filters). Nothing is displayed but errors and warnings are gathered and appear in `mh/contribute` entries (e.g. `mh/broken-docs` or `mh/common-errors`). Useful to get an overview for the state of the served archives. Crawls a maximum of 30 nodes at a time, should be re-run until finished if needed (or restarted from the beginning e.g. if the filter implementation changed).
* Lmh Update -- runs `lmh update`, basically pulls the latest version of all installed archives
* Lmh Generate -- (re)generates compiled files (that can be loaded by MMT) from source files. e.g. for sTeX, generates `.omdoc` files from `.tex` files by running LaTeXML. 
* Update Libraries -- Updates lmh internal libraries (i.e. MMT and sTeX).
* Rebuild MMT Archives -- Rebuilds MMT index, should be typically ran after Lmh Generate.

##UTF8 Setup
MySQL's default encoding is a partial UTF-8 that uses only 3 bytes max instead of 4 which means it's not adequate for some math characters.
To solve that one needs to:
* Create the database with the right defaults: 
 ```create database mathhub DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;```
* Configure MySQL to start with the "innodb_large_prefix" option to enable keys longer than 767 bytes (255 chars in utf8, 191 in in utf8mb4) which are needed by Drupal after switching to utf8mb4. It is enough to put a `mh.cnf` file in `/etc/mysql/conf.d/` containing :
```
[mysqld]
innodb_file_per_table=1
innodb_file_format=BARRACUDA
innodb_large_prefix=1
```
and then restart the mysql server.
* After initial setup update Drupal's `sites/default/settings.php` file to add
```
     'collation' => 'utf8mb4_unicode_ci',
      'charset' => 'utf8mb4',	
```
to the `$databases` settings array.
* Update drupal's database API in `includes/database/mysql/` to interface with mysql properly. You need to switch `utf8` to `utf8mb4` wherever it appears as well as add `SET NAMES utfmb4` at the connection start and make sure every table has the correct charset and collation. Additionally, you need to add `ROW_FORMAT=DYNAMIC` (or `COMPRESSED`) in the sql for table creation. Check the patch files in `patches/`.  Some of these files might be overwritten by drupal update, so make sure to keep them up to date. 

## Upgrade
Using drush `drush pm-update` is typically safest and easiest.
Currently need to take care of `scripts/run-mathhub-scripts.sh`, `misc/typeahead.bundle.min.js` and `.gitignore` being removed/overridden.