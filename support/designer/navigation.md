# Navigation

PitonCMS supports creating multiple *Navigation* lists including with drop down menus. In your web design you can define one or more Navigation lists and let the user manage Page links using the <i class="fas fa-compass"></i> **Navigation** menu.

This Navigation list manager allows the user to manage internal links, add external links, the order of links, and even create drop down link lists.

For example, if you create a `main` Navigation list, the user can add the Home and About pages as links. They can also add a Collection called "Blog Posts" (assuming this had already been created) so that new posts automatically appear in the Navigation list.

As part of the design you can define separate Navigation list for different placements, such as header, sidebar, footer and more.

## Navigation Lists
To define a Navigation list, open `structure/definitions/navigation.json` and add to the `navigators` array

```json
{
    "navigators": [
        {
            "key": "main",
            "name": "Main",
            "description": "Top of page primary navigation."
        }
    ]
}
```

To add another Navigation list, such as for a side bar, then add

```json
{
    "navigators": [
        {
            "key": "main",
            "name": "Main",
            "description": "Top of page primary navigation."
        },
        {
            "key": "sidebar",
            "name": "Sidebar",
            "description": "Sidebar navigation."
        }
    ]
}
```

After saving, open Navigation menu to see the new Navigation list.

The Navigation object has these properties

| Key | Required | Default | Description |
| --- | --- | --- | --- |
| `key` | Yes | | The unique key you will use in your templates to reference this list |
| `name` | Yes | | The display name of this Navigation list |
| `description` | | | The explanation of this Navigation list to show in the menu |

>**Note**: Once you define a Navigation key and save links, you cannot change the `key`.

## Displaying Navigation Lists
To display a Navigation list in your Template, use the PitonCMS function `getNavigator()` and pass in the `key` of your Navigation list. This will return an array of navigation entries, that you can loop over to print each link.

To print the actual anchor `href` link, be sure to use the PitonCMS function `getNavigationLink()` to derive the correct URL.

For example, to print the *Main* Navigation list with an active link class on the current page

```html
<ul class="navigation">
    {% for link in getNavigator('main') %}
        <li class="nav-item {% if link.currentPage %}active{% endif %}">
            <a href="{{ getNavigationLink(link) }}">{{ link.title }}</a>
        </li>
    {% endfor %}
</ul>
```

### Navigation Data
These properties are available in each navigation link object returned by `getNavigator()` function.

| Key | Description |
| --- | --- |
| `id` | Navigation ID |
| `navigator` | Name of navigator for this link |
| `parent_id` | If this is a child link, then this is the parent navigation ID (otherwise null) |
| `currentPage` | Boolean flag if this is the current page |
| `sort` | Numeric position of this link relative to siblings |
| `nav_title` | Override link title text defined in Navigation manager |
| `url` | Link URL (if placeholder link) |
| `collection_id` | Collection ID if part of a collection |
| `collection_title` | Collection title if part of a collection |
| `collection_slug` | Collection URL segment if part of a collection |
| `page_id` | Page ID |
| `page_title` | Page title |
| `published_date` | Page published date |
| `page_slug` | Page URL segment |
| `title` | Link title |
| `childNav` | Has child navigation array, if this parent has children |

### Child (Dropdown) Menus
PitonCMS supports dropdown child Navigation menus (just two levels deep), and if a top level navigation item has a child, that parent navigation item will have the key `childNav`, which contains the child navigation array.

To manage the HTML around this, in the Twig loop use an `if` condition to check if the current navigation item has a `childNav` to print the child navigation loop and HTML, and if not then print a normal top level link.

```html
<ul class="navigation">
    {% for link in getNavigator('main') %}
        {% if link.childNav %}
        <!-- This parent link has child navigation links -->
        <li class="nav-item {% if link.currentPage %}active{% endif %}">
            <a href="{{ getNavigationLink(link) }}">{{ link.title }}</a>
            <ul class="navigation-child">
                {% for subLink in link.childNav %}
                <li class="nav-item">
                    <a href="{{ getNavigationLink(subLink) }}">{{ subLink.title }}</a>
                </li>
                {% endfor %}
            </ul>
        </li>
        {% else %}
        <!-- This is the top level navigation link without child links -->
        <li class="nav-item {% if link.currentPage %}active{% endif %}">
            <a href="{{ getNavigationLink(link) }}">{{ link.title }}
            </a>
        </li>
        {% endif %}
    {% endfor %}
</ul>
```