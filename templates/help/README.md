# PitonCMS Help Files Structure

Help files consist of one markdown file for each link in the navbar, plus a general PitonCMS overview.

The markdown files are each named after the navbar's static route name.

We are using 'Markdown All in One' by Yu Zhang for VS Code to compile the markdown files into HMTL with a matching name.
There should be no need to edit the HTML help file, as it will be overwritten when the markdown is compiled.
This extension creates a complete HTML file with basic Microsoft CSS assets.

All help document links open open the _helpIndex.html, and the compiled HTML is loaded into an iframe on the page.

After editing the markdown file, use the VS Code menu to print to HTML.

Note: Pages and Collections share a single help file.