# Printing Twig Data

All PitonCMS user entered data is saved to a MySQL database, and when a web page is requested the Page data is made available to the Page Template as Twig variables.

Understanding how [Twig](https://twig.symfony.com/doc/3.x/) works as a designer will simplify building custom websites with PitonCMS.

## The Basics
When you include Twig variables in your custom Templates, the variable and surrounding `{{ }}` delimiters are replaced with the saved text. For example, if you want to print the *Page Title* in your HTML Layout, include

```twig
<h1>{{ page.title }}</h1>
```

and when Twig renders the Page you will see

```twig
<h1>My Life on the Water</h1>
```

When a Page is loaded the available data is injected into the Page Template as an `array`. To access a specific variable in the array reference the key using dot notation. For example, the `title` key in the `page` array is accessed as `page.title`.

If you want to print all elements in the array (such as in a list) use the Twig [for loop](https://twig.symfony.com/doc/3.x/tags/for.html) syntax

```twig
<ul>
    {% for car in cars %}
        <li>{{ car.model }}</li>
    {% endfor %}
</ul>
```

If you are unsure what variables are available in a Page, use the Twig `dump()` debugger statement to print all available variables.

```twig
{{ dump() }}
```
To get more details on a sub-array, specify the key to inspect.

```twig
{{ dump(page.blocks) }}
```

To learn more about Twig syntax, see the [Twig](https://twig.symfony.com/doc/3.x/) support documents.

## The Data Array
When a Page is requested a multi-dimensional data array is injected into each Page Template. This includes saved data for the specific page, and also site and environment data.

| Key | Sub Key | Description |
| --- | --- | ---|
| `site` | | General website data available to all Pages and Elements |
| | `settings` | Built-in and custom *Site Settings* from your JSON definition file |
| | `environment` | Application configuration information |
| `page` | | Saved content for this Page |
| | `blocks` | An array of the Page *Blocks* defined in your Template, keyed using the Block `key` from your JSON definition file. Each Block will contain all saved Element data. |
| | `settings` | A sub-array of custom Page *Settings*, if included in your Template |
| | `media` | The *Page Image* Media object, if included in your Template |
| `alert` | | System messages |

To access a variable under a sub-array, use dot notation as in `site.environment`. Other useful Page properties can be inspected with the `{{ dump() }}` command.

### Site Settings
The `site.settings` array properties are available to print in all Templates, and are defined in the project `siteSettings.json` Definition file, with user saved values for each setting.

Site Settings are useful to print small bits of data such as a link to a Twitter account, but can also be used as a flag (Y or N, or a date range for example) to control Page flow.

To print a Site Setting in a Template, specify the Setting key

```twig
<a href="{{ site.settings.twitterLink }}">Find us on Twitter</a>
```

### Site Environment
The `site.environment` array contains system and environment information such as the current user, the PitonCMS version, and production flag. These values cannot be modified but can be used in the Template layout.

There are many environment properties, including:

| Key | Description |
| --- | --- |
| `production` | Production flag (boolean, true or false) from the config file |
| `cspNonce` | The nonce to print with some elements (See [Security](/admin/support/designer/security))
| `csrfTokenName`, `csrfTokenValue` | CSRF Token required in forms |

The `production` variable is a boolean flag (true or false) that relies on the `$config['environment']['production']` setting in `config.local.php` that is unique to each environment. You can use this flag in your code to conditionally print content depending on whether the code is running on a development server or a production server.

For example, to only run tracking analytics code in your Template when in production, wrap the analytics code in a Twig `if` statement referencing the `production` flag

```twig
{% if site.environment.production %}
    <!-- My Tracking Analytics Code -->
{% endif %}
```

When building contact *Message* forms, be sure to include the [CSRF Token](https://owasp.org/www-community/attacks/csrf) in the form as a hidden input otherwise the POST request will be rejected by PitonCMS for security reasons.

```twig
<input type="hidden" name="{{ site.environment.csrfTokenName }}" value="{{ site.environment.csrfTokenValue }}">
```

### Page Blocks
Within the `page.blocks` array you will see sub-arrays for each Block you defined in your JSON definition file for this Template. Each Block requires a *unique* Block key. If you name a sidebar Block in a Page Template as `"key": "sidebar"`, then you would access Elements in this Block as `page.blocks.sidebar.<elementIndex>`.

### Page Block Elements
Within the `page.blocks.<blockKey>` array, you can acess all saved Element data, indexed by position inside the Block Key.

Within the Element HTML Layout, you can reference all `site` and `page` data arrays, plus the Block Element data in the `element` array. Within the `element` array, the main Element fields are:

| Key | Description |
| --- | --- |
| `settings` |  An array of Element [Custom Settings](/admin/support/designer/settings) objects. |
| `media` | Media properties if the Element has enabled a media selector |
| `title` | The Element Title field |
| `content` | The text content. If a Rich Text Editor was used, this content will be HTML formatted |

Furthermore, Element Templates can have some additional fields enabled by the web design. To see other Element properties use the `{{ dump(element) }}` statement in your Element Template. Read more on [Elements](/admin/support/designer/templates#element-template-example).

To access Element content use the PitoncMS template function `getBlockElementsHtml()` to print all Elements in the correct display order.

```twig
{{ getBlockElementsHtml(page.blocks.sidebar) }}
```

### Page Settings
Custom *Page Settings* behave like the Site Settings but are defined in the Page Template, and the saved values are unique to the Page URL. This is useful to define user editable structured data that do not require a content editor, such as dates, social media links, flags that control page processing, and much more.

You define Page Settings in the Page Template JSON file. See the [Page Templates](/admin/support/designer/templates#page-template-example) support document. To learn more about settings see [Custom Settings](/admin/support/designer/settings).

### Other Page Data Properties
In addition, each `page` array contains these keys to use in your templates.

| Key | Description |
| --- | --- |
| `id` | Page ID |
| `collection_slug` | Optional, collection URL segment |
| `collection_title` | Optional, collection title |
| `collection_id` | Optional, collection ID |
| `page_slug` | Page URL segment |
| `template` | Page template file to load |
| `title` | Page title |
| `sub_title` | Page subtitle (if enabled) |
| `meta_description` | Page meta description |
| `published_date` | Page published date |
| `media_id` | Featured media ID (if enabled) |
| `created_by` | User ID who created the page |
| `created_date` | Created date (which can be different from the published date) |
| `updated_by` | User ID who last updated the page |
| `updated_date` | Last updated date and time |

## Media
User uploaded media will be accessible with a standard Media array. If enabled in the Page JSON Definition file, a user can select a Page Image, either at the Page level or in an Element. The Image is accessed directly in the `page.media` array, while an image defined in an Element is accessed as the `element.media` array in the Element.

The `media` array includes:

| Key | Description |
| --- | --- |
| `aspectRatio` | Calculated image aspect ratio |
| `orientation` | Either `landscape` or `portrait` |
| `id` | Media ID |
| `filename` | Media filename |
| `width` | Original image width |
| `height` | Original image height |
| `caption` | Media caption |

The `filename` property contains the PitonCMS generated filename when the media file was uploaded. To easily print the relative path as an `img` source use the PitonCMS function `getMediaPath()`, or to generate a source set use `getMediaSrcSet()`.

## Printing Dates
Dates are stored in PitonCMS in the ISO 8601 format, YYYY-MM-DD. If you print a date variable you will get exactly that, E.g. "2020-10-05".

To print a date or datetime in a more friendly way that is localised to the user, you can provide the date format mask in the Twig `date()` filter using standard [PHP date formats](https://www.php.net/manual/en/datetime.format.php)

```twig
<div class="published-date">Published on {{ page.published_date|date('F jS, Y') }}</div>
```
Would then print
```twig
<div class="published-date">Published on October 5th, 2020</div>
```

## PitonCMS Twig Functions
In addition to the standard [Twig](https://twig.symfony.com/doc/3.x/) library, PitonCMS has a number of useful custom Twig functions you may use in your HTML Layouts.

### baseUrl()
Prints the base URL, including the scheme, domain, port, and base path (if the project is in a subfolder of your).

```php
    /**
     * Base URL
     *
     * Returns the base url including scheme, domain, port, and base path
     * @param void
     * @return string The base url
     */
    public function baseUrl(): string;
```

Usage:
```twig
{{ baseUrl() }}
```

### basePath()
Prints the base path if the project is in a subfolder of your project root.

```php
    /**
     * Base Path
     *
     * If the application is run from a directory below the project root
     * this will return the subdirectory path.
     * Use this instead of baseUrl to use relative URL's instead of absolute
     * @param void
     * @return string The base path segments
     */
    public function basePath(): string;
```

Usage:
```twig
{{ basePath() }}
```

### inUrl()
The `inUrl()` function tests if the provided string is in the current URL, and if it is then it returns a custom value. If no custom return value is defined, it returns the string 'active'. This function is useful to show the active tab or object on a custom page.

```php
    /**
     * In URL
     *
     * Checks if the supplied string is one of the current URL segments
     * @param string  $segment       URL segment to find
     * @param string  $valueToReturn Value to return if true
     * @return string|null           Returns $valueToReturn or null
     */
    public function inUrl(string $segmentToTest = null, $valueToReturn = 'active');: ?string
```
Usage:

```twig
class="inUrl('property-details')"
{# Returns 'active' if the string 'property-details' is in the current URL#}
```

```twig
class="inUrl('property-details', 'current')"
{# Returns 'current' if the string 'property-details' is in the current URL#}
```

>**Note** You can use inUrl() to print a class name, or in a Twig `if` condition, or display using `{{ }}`

### getMediaPath()
The `getMediaPath()` Template function returns the relative path to your media file and has the signature

```php
    /**
     * Get Media Path
     *
     * @param  string $filename Media file name to parse
     * @param  string $size     Media size: original|xlarge|large|small|thumb
     * @return string
     */
    public function getMediaPath(?string $filename, string $size = 'original'): ?;string
```

Usage:
```twig
<img src="{{ getMediaPath(page.media.filename, 'large') }}">
```

Pass in the `filename` and optionally a string representing the requested size (one of `'original'`, `'xlarge'`, `'large'`, `'small'`, or `'thumb'`). For example, to request the 'large' Page Image media file in your Template

And to access the `media` array in an Element reference the `element` key instead of the `page` key

```twig
<img src="{{ getMediaPath(element.media.filename, 'large') }}">
```

### getMediaSrcSet()
The `getMediaSrcSet()` Template function returns a fully formed HTML string to display the requested media file as a source set.

```php
    /**
     * Get Media Source Set
     *
     * Creates list of available image files in source set format
     * @param string $filename Media filename
     * @param string $altText  Media caption to use as alt text
     * @param array $options   Options array, includes "sizes", "style"
     * @return string
     */
    public function getMediaSrcSet(string $filename = null, string $altText = null, ;array $options = null): ?string
```

Pass in the `filename`, the alternate text (or `null`), and an optional array of `options` for `sizes` or `style`

Usage:

```twig
{{ getMediaSrcSet(page.media.filename, page.media.caption, {'sizes': '(max-width: 767px) 100vw, (max-width: 899px) 50vw, 33vw'}) }}

{# Which prints #}

<img srcset="/media/af/afe54811a462/afe54811a462-thumb.jpg 350w,
    /media/af/afe54811a462/afe54811a462-small.jpg 1024w,
    /media/af/afe54811a462/afe54811a462-large.jpg 2000w,
    /media/af/afe54811a462/afe54811a462-xlarge.jpg 3264w"
    sizes="(max-width: 767px) 100vw, (max-width: 899px) 50vw, 33vw"
    src="/media/af/afe54811a462/afe54811a462-xlarge.jpg" alt="Sunset over Terrace Gardens." >
```

>**Note**: Alternate media sizes are only created if a TinyJPG key was registered in PitonCMS and if the **Optimize** option was enabled when the media file was uploaded.

### getQueryParam()
Returns the requested Query String Paramter value, if present in the URL Query String. The returned value is escaped using the PHP function `htmlspecialchars()`. If the requested paramter is not present, then null is returned.

```php
    /**
     * Get Query String Parameter
     *
     * Returns htmlspecialchars() escaped query param
     * Missing params and empty string values are returned as null
     * @param string|null $param
     * @return string|null
     */
    public function getQueryParam(string $param = null): ?string;
```

Usage:

```twig
{# If the URL Query String includes '?size=M" then #}

{% if getQueryParam('size') == 'M' %}
    Medium
{% endif %}

{# Will display 'Medium' #}
```

### currentPath()
Returns the current URL path without the root domain, scheme, or port. Optionally, will include the Query String if present.

```php
    /**
     * Returns current path on given URI.
     *
     * @param bool $withQueryString
     * @return string
     */
    public function currentPath($withQueryString = false);
```

Usage:

```twig
{# If the current URL is https://myrecipes.com/recipe/show/242/Almond-Flour-Crackers?page=2} then #}

{{ currentPath() }}

{# returns '/recipe/show/242/Almond-Flour-Crackers'. If the withQueryString is true #}

{{ currentPath(true) }}

{# returns '/recipe/show/242/Almond-Flour-Crackers?page=2'. If the withQueryString is true #}
```

### getBlockElementsHtml()
This function returns all saved Block Elements HTML given a Block array, in the correct order.

```php
    /**
     * Get All Block Elements HTML
     *
     * Gets all Element's HTML within a Block, rendered with data
     * @param  array $block Array of Elements within a Block
     * @return string|null
     */
    public function getBlockElementsHtml(?array $block): ?string;
```

```twig
{{ getBlockElementsHtml(page.blocks.sidebar) }}
```

### getElementHtml()
Returns a single saved Block Element HTML.

>**Note**: It is recommended to use `getBlockElementsHtml()` instead to get all data for a given Block.

```php
    /**
     * Get HTML Element
     *
     * Gets Element HTML fragments rendered with data
     * @param  PitonEntity  $element Element values
     * @return string
     */
    public function getElementHtml(?PitonEntity $element): ?string;
```

Usage:

```twig
{# Assuming there is a single Element in page.blocks.sidebar #}

{{ getElementHtml(page.blocks.sidebar[0]) }}
```

### getCollectionPages()
Returns a Collection Summary given a Collection ID. Useful to create an index with cards, for a set of related pages.

```php
    /**
     * Get Collection Page List
     *
     * Get collection pages by collection ID
     * For use in page element as collection landing page
     * @param  int        $collectionId Collection ID
     * @param  int|null   $limit
     * @return array|null
     */
    public function getCollectionPages(?int $collectionId, int $limit = null): ?;array
```

Usage:

```twig
{# Get the Collection Summary set of records, and assign to a Twig variable #}
{% set collection = getCollectionPages(element.collection_id) %}

<ul>
    {% for detail in collection %}
    <a href="{{ pathFor('showPage', {'slug1': detail.collection_slug, 'slug2': detail.page_slug}) }}">
        <li class="card">

            {% if detail.media.filename %}
            <div class="card__image">
                <img src="{{ getMediaPath(detail.media.filename, 'thumb') }}">
            </div>
            {% endif %}

            <div class="card__title">
                <h3>{{ detail.title }}</h3>
            </div>

            <p class="card__text">
                {{ detail.first_element_content }}
            </p>

        </li>
    </a>
    {% endfor %}
</ul>
```

If you have a large or growing Collection Summary (such as a blog), you might consider using the `getCollectionPagesWithPagination()` function.

### getCollectionPagesWithPagination()
Returns a Collection Summary given a Collection ID, with pagination links for large Collections. Useful to create an index with cards, for a set of related pages. The PitonCMS Pagination object is automatically instantiated.

```php
    /**
     * Get Collection Page List With Pagination
     *
     * Get collection pages by collection ID
     * For use in page element as collection landing page
     * @param  int        $collectionId Collection ID
     * @param  int|null   $resultsPerPage
     * @return array|null
     */
    public function getCollectionPagesWithPagination(?int $collectionId, int ;$resultsPerPage = null): ?array
```

Usage:

```twig
{# Get the Collection Summary set of records, and assign to a Twig variable. The second argument is the number of items to return per page #}
{% set collection = getCollectionPagesWithPagination(element.collection_id, 6) %}


  <h2>{{ element.title }}</h2>
  {{ element.content }}

  <ul class="cards">
    {% for detail in collection %}
    <a href="{{ pathFor('showPage', {'slug1': detail.collection_slug, 'slug2': detail.page_slug}) }}">
      <li class="card">

        {% if detail.media.filename %}
        <div class="card__image">
          <img src="{{ getMediaPath(detail.media.filename, 'thumb') }}">
        </div>
        {% endif %}

        <div class="card__title">
          <h3>{{ detail.title }}</h3>
        </div>
        <p class="card__text">
          {{ detail.first_element_content }}
        </p>

      </li>
    </a>

    {% endfor %}
  </ul>

  {# Print pagination navigation #}
  {{ pagination() }}
```

### getGallery()
Display a Gallery of media images.

```php
    /**
     * Get Gallery by ID
     *
     * @param int $galleryId
     * @return array|null
     */
    public function getGallery(int $galleryId = null): ?array;
```

Usage:

```twig
    <div>
      {% for g in getGallery(element.gallery_id) %}
        <img src="{{ getMediaPath(g.filename, 'thumb') }}" alt="{{ g.caption }}" />
      {% endfor %}
    </div>
```

### getNavigator()
Gets the Navigator by name, with the set of user defined links in a Navigation list.

```php
    /**
     * Get Navigator
     *
     * Get navigation by name
     * @param  string $navigator
     * @return array|null
     */
    public function getNavigator(string $navigator): ?array;
```

The function returns a link object. Use `getNavigationLink()` to resolve the link object into a useable, fully formatted URL.

For Navigators with sub-menu child links, see the PitonCMS built-in navigation HTML Layout, `structure/templates/includes/_navbar.html` for an example on looping through sub-menu links.

Usage:

```twig
<ul>
    {% for link in getNavigator('main') %}
{# Use the `currentPage` attribute to test if this is the active link #}
        <li class="{% if link.currentPage %}active{% endif %}">
{# Use the `getNavigationLink()` function return a formatted URL #}
            <a href="{{ getNavigationLink(link) }}">{{ link.title }}
          </a>
    {% endfor %}
</ul>
```

### getNavigationLink()
Returns a complete resolved URL given a Navigator link.

```php
    /**
     * Get Navigation Link
     *
     * @param PitonEntity $navLink
     * @return string|null
     */
    public function getNavigationLink(PitonEntity $navLink): ?string;
```

Usage:

```twig
    {{ <a href="{{ getNavigationLink(navLink) }}">{{ navLink.title }}
          </a> }}
```

