# Pages and Collection Detail Pages

>This support document covers creating and updating content for both *Pages* and *Collection Details*. To learn about *Collections* see the [Collections](/admin/support/client/collections) support document.

## What are Pages
Websites consist of pages that hold various types of content (words, links, images) and are accessible at a unique URL. In PitonCMS, *Pages* hold content that rarely changes such as Home, About, Location etc. and have links to the Page in the main website Navigation. *Collection Detail* Pages are similar, but are meant to group Pages with similar content together, such as recipes (or even a cateogry of recipe), events, services etc.

The main difference between a Page and a Collection Detail is published Pages need to have a link manually added to the Navigation lists to be accessible, while published Collection Detail Pages have a link automatically added to the *Collection Summary* for that collection.

In the support document below creating and modifying content is the same whether you are working on a Page or a Collection Detail.

## Creating a New Page
You can create a new Page by going to the <i class="fas fa-pencil-alt"></i> **Content** menu and then click on the **Add Page** menu link in the top right corner and selecting a **Template** layout to use for the new Page. Note, once you create a Page you cannot change the Template you selected, but you can delete the Page and recreate it.

## Editing a Page
You can modify a Page by finding it in the list of Pages in the **Content** menu and clicking on the row to open it in the editor. You may need to filter or search to find the Page you are wanting to modify.

## Saving and Publishing
Once you have created or modified your Page you can save your changes by clicking the **Save** button in the top right corner. You can also cancel any changes to an existing (saved) Page by clicking **Discard**.

To make your Page content available on the web, you need to *Publish* your page by setting the **Publish Date** to today's date or a date in the past. You can schedule new content to be Published by setting the Publish Date to the desired future date.

The Publish Date field controls the *Page Status*
- **Draft** No Publish Date has been set, and the page is *not* visible to the public
- **Pending** A future Publish Date has been set, and the page is *not* visible to the public
- **Published** The Publish Date is today or in the past, and the page *is* visible to the public

## Page Structure, Templates, Blocks, Elements
Pages and Collection Detail Pages are created based *Templates* for the layout. These Templates were created as part of your website by your web designer. Once a Page is created you cannot change the Template.

Within each Page Template, the content is broken up in to *Blocks*, which represent areas within the Page such as main content area, sidebar, hero, etc. You cannot change the arrangement of these Blocks.

To these Blocks you can add one or more *Elements* that hold content displayed on your page. Your Page might have one Block and one Element, or many of each. There are different types of Elements as defined by your web designer. Your web design may have defined these Blocks to only allow a limited number of Elements or a specific type of Elements.

In this example of the "With Hero" Template layout, the design defines two Blocks ("Hero" and "Content") to appear on the page, to which you can can add content Elements when editing the page.

![Page Template Overview"](/admin/img/support/pageBlockElementOverview.png)

## Other Page Fields
In addition to Elements, you will also see these other fields that hold additional information for the page.

- **Page Title** The display name for the page
  -  Your web designer may also enable an optional **Sub Title**
- **Meta Description** You can add a brief (320 character) summary of the article. This field is provided to search engines which may use it to display with search results
- **Page Image** If enabled by your designer, this is an image that may be displayed with the page.
- **Custom Page Settings** or **Custom Element Settings** Optional information per your design
- **Publish Date** The date this Page should be available to the public.
- **Page Slug** The URL link to this Page.

## Linking to Your Page - the Slug
The *Page Slug* the portion of the URL (web address) that starts with a forward slash `/` and comes after your domain. To avoid issues with case-sensitive URL's, all Slugs are forced to lowercase and special characters are removed or converted to dashes.

In PitonCMS *standard* Pages have a simple URL structure with one segment (the text between slashes `/`). The Slug is initially based on the Page Title, but you can modify the Slug if you wish to provide clarity or brevity.

>Note: to change the slug *after* the Page is Published you must first click the <i class="fas fa-lock"></i> lock icon and acknowledge the warning. Changing the Slug after the Page is published may break links to your Page!

>Note: The `home` Slug is restricted from being changed.

Collection Detail Pages have an additional segment for the Collection after the domain and before the Page Slug. This *Collection Slug* is unique to each Collection.

## Custom Settings
Your web designer may include custom Page or Element settings, which are specific and granular information to be used in the page. Examples may include

* The link URL and text for a call to action button
* The color of a Page feature that you can change
* A banner text to display on the page

If you do not see any Custom Settings, then the Template you are using does not include any.

### Media
To use media (images and files) in your content, you should first upload and categorize your media in the <i class="fas fa-image"></i> **Media** menu. See the [Media help](/admin/support/client/media) support document for more information.

## How to Delete a Page
To delete a Page, find the Page in **Content** menu and click on the row. Press the **Delete Page** button at the bottom of the page and acknowledge the warning prompt. The Page and all associated information will be permanently deleted.

## Add the Page to Navigation
Your custom website design may likely have some *Navigation* lists, which have links to site Pages. When creating a Page you need to *manually* add the Page to one or more Navigation lists so that visitors and search engines can find your Page. The number of Navigation lists and their positions is determined by your web design. See the [Navigation ](/admin/support/client/navigation) support document for more information.
