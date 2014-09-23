=== Document Importer by Plugmatter Lite ===
Contributors: SNaimath, Akramquraishi
Donate link: http://plugmatter.com/
Tags: document importer, word document importer, docx, MS Word, Microsoft Word, Google Docs, Dropbox, WordPress Visual Editor, Formatting, Content Formatting
Requires at least: 3.7.0
Tested up to: 3.9 and above
Stable tag: 1.1
License: Free
License URI: http://plugmatter.com/document-importer/

== Description ==

Document Importer - Quickly import your DocX file into your WordPress editor without losing any formation.

Plugmatter Document Importer is a WordPress plugin that lets you effortlessly import your document content directly into WordPress Editor. Our plugin simplifies your content publishing by converting the lengthy content editing/formatting task into a few click process. 

With Plugmatter Document Importer, you no longer have to:

* Manually Upload Images into Your WordPress
* Update links in content one after the other
* Format sub-headings, bulleted points, header tags, quotes, etc.

Note: It only imports .docx files.

==== How It Works? ==== 
After you install the plugin, a plugin widget appears right above your “Publish” Widget in a New Post. Just import your MS Word file or any other document in our plugin and atch the plugin do its work. It will paste the content "EXACTLY" how it is in your document.

You can select your word documents i.e. MS Word, Libera, OpenOffice files from: 

1. Computer
2. Google Drive
3. Dropbox

Note: The Word documents must be in ‘.docx’ file format and not ‘.doc’ format for them to get uploaded effectively.

==== Who is Plugmatter Document Importer For? ==== 
For every WordPress use Microsoft Word and Google Docs to create content.

==== Features ==== 

* Import Document Directly into Your WordPress Editor

* Import Document from:
- Your Computer 
- Dropbox
- Google Drive 

==== Support ====
For any help or support requests, please email us at support@plugmatter.com with Subject: "WordPress Document Importer- Your Problem”

== Installation ==

You can install Document Importer by Plugmatter in different / traditional ways:

WordPress Search:
----

1. Use WordPress Plugin Search in your WordPress
2. Look for "Document Importer by Plugmatter"
3. Click to Install and Activate
4. A new Document Importer by Plugmatter will appear in the "Settings" in the left menu

Download & Install:
----

1. You can download the ".zip" file from the top right of this page on to computer
2. Go to your Plugins section and click "add new"
3. Select the tab "Add"
4. Click "Upload" to upload and install the plugin from your computer / desktop
5. Once installed, activate the plugin
6. A new Document Importer by Plugmatter will appear on your Admin column

FTP:
----

1. You can also install the plugin using the FTP
2. Simply Download the ".zip" file of Plugin from top right of this page
3. Login to your WordPress FTP
4. Upload the ".zip" file in your wp-content > Plugins
5. Login to your WordPress and Activate the Plugin
6. A new Document Importer by Plugmatter will appear on your Admin column


==== Google Drive API Setup Tutorial ====

Step 1 – Visit https://console.developers.google.com, Enter your Google login details if you are not already logged in. If you’re logged in, it would directly take you to the console page.

Step 2 – Click on “Create Project” 
	• Enter The Project Name (Example: GD API, DocImporter API) in the “New Project” pop-up window.

	• Check mark on the “Terms of Service” line.

	• Click “Create”

Within a few seconds, your new project would be created. 

Step 3 – On the left Menu Bar, click on “APIs & auth” then click on “Credentials” listed under API & auth

Step 4 – Click on “CREATE NEW CLIENT ID” 
	• In the pop-up “Create ClientID,” enter your website URL (don’t forget adding http:// in the URL. Example: http://www.example.com/) in Authorized JavaScripts 	origins.

	• In the second box “Authorized Redirect URL,” enter the below URL by simply changing your domain name:
	http://yourwebsite.com/wp-admin/admin-ajax.php?action=google_callback

	Once adding the two URL’s, press create client ID.

Step 5 – Click on “CREATE NEW” Button 
	• On the new popup click on “Browser Key” button
	• On the latest popup, just leave the textbox empty and click on “Create” button

Now you can see an API key below “Key for Browser Applications” 

Copy the following to post in your WordPress:
	• Client Id
	• Client_Secret
	• Redirect_URL 
	• API Key 

And paste it in Google API Settings of DOCUMENT IMPORTER Settings page.
 
Step 5 – Now get back to the Google Console Panel, click on “APIs” within “APIs and auth” you can see a list of Google APIs on the right. Turn “On” the following APIs: 
	1. Groups Settings API
	2. Google+ API
	3. Google Picker API
	4. Drive SDK 
	5. Drive API

That’s It. You can then start importing your documents from Google Drive. 

==== Dropbox API Setup ====

Step 1 – Visit https://www.dropbox.com/developers and click on “App Console” on the left side menu bar.

Step 2 – You will see ‘Dropbox API terms and conditions.’ Click on ‘I agree’ and press “Submit,” 
You will then be redirected to the page “Create a new Dropbox Platform app.

Step 3 – Click on the “Drop-ins app” then a textbox appears asking you to enter APP name, enter the APP name (Example: DB API, DocImporter DB API) and then click “Create app” button.

Step 4 – Once submitted, you can see four rows namely 
	1. Status
	2. Permission Type
	3. App Key
	4. Drop-ins domains

Enter the URL of Your Website in “Drop-ins domains” field and click “Add.”

Step 5 – Lastly copy the “App Key” and paste it in DropBox API Settings of DOCUMENT IMPORTER Settings page on your WordPress. 
That’s it, you can now import files from your Dropbox.


== Frequently Asked Questions ==
What file format does the plugin import?
Document Importer by Plugmatter only imports .docx file format as of now. So before importing the document, make sure that it is in .docx format.

How is this different from “Paste from Word”?
Document Importer is different from Paste from Word in various ways. Our plugin uses advance XSLT parser to convert a word file into a HTML file, which retain all the formatting when imported. 

Our plugin not only imports images with the content but also adds them to media gallery, automatically. This allows you to use the images for future purposes.
Where do I go for technical support?
You can send us an email at support@plugmatter.com with your query and our dedicated support team will get back to you as soon as possible.

Can you help me with API Setup of Google Drive and Dropbox?
Of course, we can help with your API Setup. You can follow our Document Importer by Plugmatter user guide to learn how to do it. However, if you have any concerns concerns, you can always email us at support@plugmatter.com

Will Document Importer by Plugmatter work on WordPress?
No. Document Importer by Plugmatter is not compatible with WordPress.com (the hosted version of WordPress). This is because WordPress.com doesn't allow its users to install any third-party plugins.

Will Plugmatter Optin Feature Box work on Joomla or Drupal sites?
No. Document Importer by Plugmatter has been specifically developed to work with WordPress. It leverages core WordPress functionality and therefore will not work on any other CMS (content management system).

== Screenshots ==


== Changelog ==

### Version 1.1 ###

Enhanced multiple browser compatibility 

### Version 1.0 ###

### Version 1.0 ###

### Version 1.0 ###

== Upgrade Notice ==
There's a new version of Plugmatter Document Importer Lite i.e., version 1.1