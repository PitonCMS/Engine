# Media

Before you can display and share various media (images, files) you need to first upload them to PitonCMS as *Media*. Once uploaded you can display images and share files in your website content.

PitonCMS has a robust Media manager to categorize, label, and feature images. PitonCMS can also optimize JPG and PNG images (with a TinyJPG key) for use in your website.

>**Note**: For videos PitonCMS recommends hosting your video with any one of the popular video streaming services such as [YouTube](https://youtube.com) or [Vimeo](https://vimeo.com/). You can then embed the video player HTML into your PitonCMS Page as an *Embedded Element*..

## Optimize Images With TinyJPG

[TinyJPG](https://tinyjpg.com/) is a web service to optimize and compress your JPG and PNG files for use on the web. TinyJPG is free to use for the first 500 image operations per month, more than enough for most uses with PitonCMS.

If you register your TinyJPG key in PitonCMS, PitonCMS will create optimized thumbnail and alternate sizes for use throughout your website to support faster page loads. TinyJPG is optional to use, but without a Tiny API key only the original full sized images will be used on your website, with possibly a slower visitor experience.

To add a TinyJPG key
1. Go to [Tiny API](https://tinyjpg.com/developers) and enter your name and email address and click **Get your API key**
2. Once you get the TinyJPG login link in your email, follow the link to TinyJPG
3. Click on your name and **Account page**
4. Copy the **API key**
5. In PitonCMS go to <i class="fas fa-cog"></i> **Settings** menu then click on **Site** and paste your key in **Tinify API Key**
6. Click **Save**

When you upload Media files PitonCMS will request TinyJPG to optimize your JPG and PNG files and create different sized copies optimized for the visitors device and browser. You can also disable TinyJPG optimization for any one upload.

## Managing Media Images
To view and modify media attributes for your uploaded images, go to the <i class="fas fa-camera"></i> **Media** menu. Here you will see all of your uploaded files.

If you do not see the file you are looking for, you can **Search** on the Caption, filter on the **Media Category**, or filter on the **Featured Image** status.

Each image is presented in a *Media Card* where you can edit attributes for that image.

<img src="/admin/img/support/mediaCard.png" style="width:50%;">

The Media Card

* <i class="fas fa-link"></i> Click to copy a relative link to this Media file
* (Media thumbnail) Click the Media thumbnail to view a full size version
* **Caption** View or edit the Caption
* **Featured Media** Check the box to flag this file as a *Featured Media* for enhanced display
* **Categories** Check one or more relevant *Media Categories*

If you have a *Gallery Page Element* in a Page, you can display a Media Category of images in a Gallery format. A Media File can be in more than one Gallery.

After making your changes to a Media File, click on **Save** to save your changes, or **Discard** to reset your changes.

To delete a Media File click **Delete** on the the card and acknowledge the warning.

>**Note**: Deleting a Media File is immediate and permanent, and will remove it from all of your content.

## Media Image Categories

Before you upload your first image, consider creating some *Media Category* to categorize and find your Media later. You can add, remove, and alter gallery categories later, but it is easiest if you have one or two at the start.

To create or edit a Media Category
1. Go to <i class="fas fa-cog"></i> **Settings** menu then **Media Categories**
2. Click the **Add** link in the top right corner and then **Category**
3. Enter a display name for this Media Category
4. Click **Save** to save your changes or **Discard** to reset your changes

You can also rename a Media Category by updating the display title and pressing **Save**.

You can also safely delete any Media Cateogry by pressing the **Delete** button. This only removes the category and image associations, not the media files.

>**Note**: Deleting a Media Category is permanent and immediate.

## How to Upload Media Files

To upload a Media file, click on the <i class="fas fa-camera"></i> Media menu, then on **Manage Media** in the top right corner, and then **Add Media** link. This opens a modal where you can select the file to upload.

![Media Modal](/admin/img/support/mediaUploadModal.png)

The upload modal fields include

* **Choose File** Click to select a media file on your computer
* **Featured Media** A flag that can be set that might give this image an enhanced position when viewed in a *Gallery*
* **Categories** A list of the Media Categories you created
* **Media Caption** A text caption to display with the image
* **Optimize Media** If you have a TinyJPG key set this option will create multiple sized and optimized copies of your JPG and PNG files

Except for the **Choose File** input all fields are optional. You can also edit all fields at anytime.

>**Note**: The *Featured Media* flag is a display option that requires a supported web design.

Select the file to upload, enter or check the desired options, and press **Upload**.

If you selected **Optimize Media** PitonCMS will request optimized copies of your image from TinyJPG after completing the upload. You will see a notification when the optimization process is complete.

## Using Media Files in Your Content
Once your Media Files have been uploaded, you can display or reference them in your Content.

Ways to display your media include

* **Gallery** In a *Gallery Page Element* in Page content
* **Page Image** or **Hero Image** Selecting a media file in your Page editor or Page Element
* **Link** Paste a link to the media file in your text editor
