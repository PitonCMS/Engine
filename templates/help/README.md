# PitonCMS Help Files Structure

Help files consist of one markdown file for each link in the navbar, plus a general PitonCMS overview.

The markdown files are each named after the navbar's static route name.

We are using 'Markdown All in One' by Yu Zhang for VS Code to compile the markdown files into HMTL with a matching name.
* There should be no need to edit the HTML help file, as it will be overwritten when the markdown is compiled
* This extension creates a complete HTML file with basic Microsoft CSS assets
* There is an extension option to Print File on Save, which auto compiles to HTML

All help document links open open the _helpIndex.html, and the compiled HTML is loaded into an iframe on the page.

After editing the markdown file, use the VS Code menu to print to HTML (or set extension to option to print on save).

Note: Pages and Collections share a single help file.

Help links have three segments:
* `help` Site level help, no content loaded
* `help/<helpFile>` Loads specific help content file
* `help/<helpFile>/<headingAnchor>` Loads specific help content file and scrolls section into view

Note: The `<headingAnchor>` may change if the markdown section heading changes, which means you have to find and update deep links to help content sections.
