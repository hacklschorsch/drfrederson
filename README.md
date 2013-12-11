drfrederson
=====

A simple static site generator powered by PHP, YAML, Markdown and mustache. Based on [github.com/lonescript/php-site-maker](http://github.com/lonescript/php-site-maker)


There are many other and better [staticsitegenerators](http://staticsitegenerators.net), but I searched for this features and did not found:

* edit without texteditor or ssh access. Using __webedit__ on your browser and get a textarea to change and create pages.
* __meta information__ for rendering (template, menue etc) or html metatags are at the __bottom__ of the page and only __optional__
* no indexed or automatic __menu__; just an extra included __page__ with a (nested) list.

Some more small features like titletags from the first heading, etc.

## Write

Write files in [markdown extra](http://michelf.ca/projects/php-markdown/extra/).
All source code for content is stored in ''source/''

Edit your website in different ways:

* __normal__ edit source files on filesystem and with your favourite text-editor. After editing call `./make.sh` in you CLI.
* __webedit__ with the edit button on the page. After 'save' the single page will be created. If data2css (d2c) class `noedit` is set, webedit is veiled, but not prevented,
* receive changes via __git__ (or other __DCVS__) and update the source and call make.php manually or with a hook.
* there is __no__ file and assethandling, just upload pictures and files in ths target directory. There is no reason why a static-site-generator should copy files around. And if you wish some filehandling like renaming, image resizing and cropping, use a specialized software.


## Administration

There is no extra admininterface, edit existing pages with the edit button or create new pages with the error page. All information for the working admin on [admin.md](source/admin.md).

Configuration is editable on each page after the `#meta#` and in the [config](config)-page.
Sidebar, menues and other page-durable content is stored like a ''normal'' page on [navigation](source/area/navigation.md) and [sidebar](source/area/sidebar.md) (the concept is well adapted in [Mediawiki Sidebar](https://www.mediawiki.org/wiki/Manual:Interface/Sidebar)).

## Style and JS

CSS and Javascript libraries are maintained in `lib/` as [submodule](http://git-scm.com/book/en/Git-Tools-Submodules)

* [CSS-Mini-Reset](https://github.com/vladocar/CSS-Mini-Reset/)
* [usefulclassroomphrases](https://github.com/klml/usefulclassroomphrases)
* [skeleton](https://github.com/dhg/Skeleton)

Additionally you can have css and js in your template directory and local custom files in the `source`.

### Custom

Edit custom client-sources (css, js) in [drf-custom-css](source/drf-custom-css.css) and [drf-custom-js](source/drf-custom-js.js) as "normal" pages
(the concept is well adapted in [Mediawiki UseSiteCss](https://www.mediawiki.org/wiki/Manual:$wgUseSiteCss) and [SiteJs](https://www.mediawiki.org/wiki/Manual:%24wgUseSiteJs)).


## Setup

Clone or [download](https://github.com/klml/drfrederson/archive/master.zip), create config from .example and create first version of site.
Used libraries are included as [submodules](http://git-scm.com/book/en/Git-Tools-Submodules).

The public site, where ever it is located (other server, CDN, etc), needs only html and assets (images, css, js) files.
The `_drf/`-directory is needed for changing content. The `make.php` has **no role or user validation**, protect this against violaton:

* .htaccess for the whole *_drf/*-directory. (Change existing .htaccess to a suitable [Basic access authentication](http://en.wikipedia.org/wiki/Basic_access_authentication))
* use editing only on secure machines like your desktop or intranet and publish all without *_drf/* (e.g. `rsync --exclude=_drf/`)

Run `./make.sh` or [_drf/make.php](_drf/make.php) for the first run.

```
git clone https://github.com/klml/drfrederson.git
cd drfrederson/
git submodule init
git submodule update 
cp _drf/config.yml.example _drf/config.yml
./make.sh
echo 'AuthUserFile '"$( readlink -f _drf/.htpasswd  )" >> _drf/.htaccess
htpasswd -c _drf/.htpasswd USERNAME
```

## make

All processing in [_drf/make.php](_drf/make.php) to create static HTML-files from `source/` 'compiled' with all `template/` to `html/`.

Now the __webserver__ can deliver pure html files. If you want an URL without the extension .html, [rewrite](http://stackoverflow.com/questions/10245032/url-rewrite-remove-html/10279744#10279744) in [.htaccess](.htaccess).

### config.yml

Metainformation (template, comments, meta-description, title etc) can be defined in [yaml](http://www.yaml.org/spec/1.2/spec.html)

* for __each page__ at the bottom of sourcefile after the `#meta#` (can be redefined in config 'ymlseparator').
* or for each __directory__ in `config.yml` or for the whole site in `source/`.
* and as fallback and for filepathes in `_drf/config.yml`.

#### site wide

site-wide configurations, only usable in `_drf/config.yml`.

siteName
: mostly used in the html title,  

baseurl
: root url *http://example.com* or *http://example.com/mypage* for absolute URL inside the html. Works also with relative (leave empty).

htmlextension
: filename extension for html output. Mostly '.html'.

ymlseparator
: tag in pages to seperate content and config (`#meta#`).

path
: filepath for templates, text-source and target for html.

#### page specific

Are used for every single page, overwrites `_drf/config.yml`.

layout
: template

title and description
: [HTML Title Element](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/title) and meta description

comment
: allow comments TODO

area
: defines filepath for navigation, sidebar and footer; parts of the page always appear like the menu.

d2c
: [data2css](http://umija.org/howto:data2css) adds a class to html-body

cssjsversion
: versioning css and js


## templates

Using [mustache](https://github.com/bobthecow/mustache.php), it is [logic-less in a religious way](http://upstatement.com/blog/2013/10/comparing-php-template-languages-for-wordpresss/), but you can use [mustache](http://mustache.github.io/) in JavaScript on your client.(TODO;)

There are three bundle of data in the template:

* *source* with raw content (e.g markdown) and [pathinfo](http://php.net/manual/de/function.pathinfo.php) from your sourcefile
* *meta* with all nested [configuration](#configyml) from source directories and your page
* *content* contains all html, structured in __areas__, according [wai-aria landmarks](http://www.w3.org/TR/wai-aria/roles#landmark). Area sourcefiles are defined in config, only the area 'main' is the page prose itself.

For [webedit](#Write) add the [HTML metatag](http://www.w3.org/wiki/HTML/Elements/meta) [dcterms.source](http://dublincore.org/documents/dcmi-terms/#terms-source) (referenced from [whatwg.org MetaExtensions](http://wiki.whatwg.org/wiki/MetaExtensions#Registered_Extensions) ) with the path to source (e.g. markdown) `source.websourcepath`.

```
<link rel="schema.dcterms" href="http://purl.org/dc/terms/">
<meta name="dcterms.source" content=""/>
```

Existing templates

* `template/skeleton.html` [getskeleton.com](http://www.getskeleton.com)
* `template/plain.html` with nothing


## TODO

- htaccess setup
- mod for no htaccess
- lib: https://github.com/lepture/editor
- redirects with page
- http://php.net/manual/de/function.realpath.php PHP 5.3
- source versioning
  - http://stackoverflow.com/questions/7447472/how-could-i-display-the-current-git-branch-name-at-the-top-of-the-page-of-my-de
- source config without passwords etc
- trailing slash
- 404 to root
- _drf/config.yml to js serversite https://github.com/coolaj86/yamltojson.com
- new article field (and include)
- [ ] include http://www.mediawiki.org/wiki/Transclusion; Mustache ?
- [ ]* index: dir as link include (for pix)
- more cutter
- [ ] demo
- [ ] minfy
- [ ] CSS-Mini-Reset realy needed
- [ ] https://github.com/blueimp/jQuery-File-Upload
- [ ] URL builing? root dir : absolute relative only one dir
- [ ]* directory to namespace
- [ ]* directory doubled
- [ ]* global filenamepath
- [ ] test if yml exists from func
- [ ] bloggish posttemplate (date, archivcat) for webedit
- [ ] tags
- [ ] lasttest
- [ ] etherpad
- [ ] doku images timestamp


### Refactor

- [ ] 404 er save
- [ ] lib https://github.com/michelf/php-markdown extra (needs php 5.3)
