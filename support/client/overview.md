# Overview

PitonCMS is an easy to use, powerful website *Content Management System* (CMS) for personal and small business websites. Your website designer has configured and built this website for your specific requirements, and this administration console allows you to easily modify and manage your website content.

If you are new to your website, here are a few things to get you started.

## How to Log In
PitonCMS uses a passwordless login, and does not store confidential passwords. When your website was setup by your designer, your email address was added as a registered user. To login, simply go to your website and add "[/login](/login)" to the URL and submit.

Enter your email address and click the **Request Login Link** button. If the submitted email matches a known user registered in PitonCMS, a link will be sent to you with a one-time use login token that takes you to the PitonCMS administration console. The link sent to you expires in 15 minutes, and cannot be reused.

Once logged in, your session remains active for about two hours after your last interaction. Your web designer can change this setting to increase or decrease the session timeout.

Note, for security reasons PitonCMS login links are unique to the device and web browser. This means you must request the PitonCMS login link from the _same_ device (laptop, phone, tablet etc.) that you use to access your email. Each device you use will require a separate login request.

## Modify Website Settings
Some important aspects of your website is controlled by *Settings* you can change as needed. Go to the <i class="fas fa-cog"></i> **Settings** menu and browse the Settings to ensure they are correct.

The main Settings to review are

* **Site** Browse the site wide settings and confirm the values are correct.
* **Contact** If you have a contact form on your website enter your email address to have contact messages submitted to your website forwarded directly to you. If you do not set a forwarding email, you can still view messages under **Mailbox**.
* **Social** Add links to your social media accounts.
* **Users** Consider adding another user account, or a back up administrator email address.

You can find more information in the [Settings](/admin/support/client/settings) support document.

## Manage Website Content
Most of your website content can be managed from the <i class="fas fa-pencil-alt"></i> **Content** menu.

The building blocks of any website are pages that have content accessible at a unique URL. PitonCMS *Pages* can either be standalone or part of a collection of related Pages called a *Collection*.

A Page typically has content that changes infrequently such as Home, About, Location etc. and is listed in the main website Navigation.

*Collection Details* are a group of related Pages such as blog posts, recipes, services, or a category of content. An individual Collection Detail is accessible at a unique URL, but all content within that collection share a common URL segment.

When a new Collection Detail is published a link is also automatically added to the *Collection Summary* for that Collection. A Collection Summary is the set of related links to published content within that collection. This makes for an easy to way to publish blog posts and new recipes without having to update any Navigation.

You can have multiple Collections on your website, to categorize content.

## Page Structure
The Page and Collection Detail layouts (the look and structure of the page) are based on the *Templates* created by your website designer. A single Template can be reused on multiple Pages.

Templates contain *Blocks* that represent areas of the page layout, to which you can add one or more *Elements* that contain your actual page content.

There may also be *Custom Page Settings* or *Custom Element Settings* in the Template. These are bits of information to enhance how page the displays (as intended by your website designer).

You can pre-publish Pages by setting the *Publish Date* to a future date.

You can find more information on how to manage content in the [Pages](/admin/support/client/pages), or the [Collections](/admin/support/client/collections) support documents.

## Website Navigation Links
You can define how internal links appear in your website's *Navigation* (the list of links to browse your website), including the order of links, link text, and also dropdown links in the <i class="fas fa-compass"></i> **Navigation** menu.

You can find more information in the [Navigation](/admin/support/client/navigation) support document.

## Upload Media Files
Before you can display images and other media on this website you need to upload the media files from the <i class="fas fa-images"></i> **Media** menu.

You can upload any media file type, but video and other large graphics should be hosted on a video streaming platform such as [YouTube](https://youtube.com) or [Vimeo](https://vimeo.com/). You can then embed the video player HTML into your PitonCMS page as an Element.

PitonCMS recommends getting a [Tinify](https://tinyjpg.com/) key to optimize image files. You can get a free key that supports 500 media operations a month (about 100 PitonCMS image uploads). Go to [Tiny Developer API](https://tinyjpg.com/developers) and enter your email address to receive a key that you save in **Settings > Site > Tinify API Key**. Your web designer can help you set this up.

You can find more information in the [Media](/admin/support/client/media) support document.

## Mailbox and Messages
If your site has a contact form enabled, your contact messages are saved under the <i class="fas fa-envelope"></i> **Mailbox** menu. Here you can search, delete, or archive your messages submitted on your website.

You can find more information in the [Messages](/admin/support/client/messages) support document.

## Issues
If you encounter issues with PitonCMS, please submit them on [GitHub PitonCMS](https://github.com/PitonCMS/Piton/issues), or contact your website designer.