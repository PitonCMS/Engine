# Overview

PitonCMS is an easy to use, powerful Content Management System for personal and small business websites.

## Logging In
PitonCMS does not store confidential passwords. When your website was setup by your designer, your email address was added as a registered user. To login, simply go to your website and add "[/login](/login)" to the URL.

Enter your email address and click **Request Login**. A login link will be sent to you with a one-time use login token that takes you to PitonCMS administration pages. The link expires in 15 minutes, and cannot be reused. Note, you must request the login link from the _same_ device you use for your email to open the link. Each device you use will require a separate login request and link.

## Quick Start

If you are new to your custom website, here are a few things to review:

### Tools Menu
* **General** Browse the site wide settings and confirm the values are correct. These can be changed at anytime.
  * Under **General**, consider adding a [Tinify](https://tinyjpg.com/) key to optimize uploaded media.
* **Contact** If you have a contact form on your website enter your email address to receive website contact messages directly to you, and set the acknowledgement message after the contact form is submitted.
* **Social** Add links to your social media sites.
* **Users** Consider adding another user account, or a back up administrator email address.

And of course, browse the support documents.

## Key Concepts
When your web designer planned your website with PitonCMS, they defined the general structure and layout of pages, the navigation bars, and overall appearance of your website. You can easily manage the content within this structure from the Administration pages.

### Content Menu
Most of the website content and navigation can be managed from the **Content** menu.

#### Pages
Pages have content accessible at a specific URL. Typically these hold content such as Home, About, Location, and Collection Summary etc. and are part of the main website navigation.

Page and Collection Detail Page templates contain **Blocks** that represent broad areas of a page template, to which you can add one or more **Elements** that contain your page content and media.

There may also be designer defined **Custom Page Settings** in the page template, that store specific bits of information for the page.

You can pre-publish pages by setting the publish date to a future date.

#### Collection Pages
Collections are groups of *related* pages such as blog posts, recipes, services etc. and consist of **Collection Detail Pages**. **Collection Summaries** are group of links to the collection detail pages (think of the collection summary page as an index pointing to the detail pages).

You can have multiple collections on your website, such as separate collection groups for different recipe categories.

#### Navigation
You can define how page links appear in your site's navigation, including the order of links, link text, and sub-menu links. Only *Pages* appear in navigation links, Collection Detail Pages are linked from a Collection Summary static page.

### Media
Before you can display images and other media in your pages or collection detail pages, you need to upload the media file from the **Media** menu. You can upload any media image or PDF file type, but video and other large graphics should be hosted on a video streaming platform such as [YouTube](https://youtube.com) or [Vimeo](https://vimeo.com/). You can then embed the video player HTML into your PitonCMS page as an element.

PitonCMS recommends getting a [Tinify](https://tinyjpg.com/) key to optimize image files. You can get a free key that supports 500 media operations a month (about 100 PitonCMS image uploads). Go to [Tiny Developer API](https://tinyjpg.com/developers) and enter your email address to receive a key that you save in **Tools > General** Site Settings.

### Messages
If you have a contact form enabled, your contact messages are saved under the **Messages** menu. You can search, delete, or archive your messages.

### Tools
PitonCMS has a collection of website management tools under the **Tools** menu, including:

* General Site Settings
* Contact Form Settings
* Social Media Links
* Website Administration Users
* Sitemap to help Search Engine Optimization

## Issues
If you encounter issues with PitonCMS, please submit them on [GitHub PitonCMS](https://github.com/PitonCMS/Piton/issues).