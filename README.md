Extended Comingsoon Countdown
=============================

A simple but complete comingsoon countdown with Progressbar and Clock
Check a working [Demo](http://razorphyn.com/products/comingsoon/admin/)

Installation
-
-	Unzip the rar;
-	Copy the content of the folder comingsoon  inside the root of  your server;
How to Start
-
-	To access to your admin:
-	Type admin at the end of your url (ex http://mysite.com/comingsoon/admin)
 
-	Start to setup your page.
Database Files Checking
-
-	Check Database Files: check for missed database files and reset the file permissions
SETUP
-
**Logo**
-	Logo: this is your current logo;
-	Upload a New Logo:	upload your new logo, accepted extensions: png, jpeg, jpg, gif;
 			Max file size: 5 MB.

Frontend
-
-	**Page Title**: set the name of the page;
-	**Finished site url**: you will be redirected to this url when the countdown will end;
-	**Use FitText?**:enable or disable FitText
-	**Site phrase**: the main site phrase that will be displayed
-	**Starting data**: the date when you have started to build your site (needed for time percent)
-	**Starting hour – minute and second**: the time when you have started to build your site (needed for
time percent)
-	**Release date**: the date when you will release the site;
-	**Release hour – minute –second**: the time when you will release the site;
-	**Percent (0-100)**: set this to use personal percent instead of time percent;
-	**Admin email**: default admin email where you will receive the emails;
-	**Show contact form**: show frontend contact form;
-	**Show subscribe form**: show frontend subscribe form;
-	**Show Unsubscribe Link Inside Email Footer**: show inside the email footer the unsubscribing link;
-	**Server Email Restriction**: some server may have a mail policy restriction such as max number of email per hour. Example: with Dreamhost you can send 200 mail per hour or with Hostgator 500 per hour, you have just to set this limit, remember to set the time in seconds;
-	**Time Zone** (Continent/City; ex Europe/London): set your time zone, you can use the autocomplete feature;
-	**Show Frontend Clock**: show or hide the countdown clock;
-	**Show Frontend Progressbar**: show or hide the progressbar;
-	**Progressbar Phrase**: the sentence that will be displayed before the progressbar
-	**Footer Phrase**: the sentence that will be displayed at the bottom of the page


Social Network Link
--

Insert your social network links (only completed field will be showed)


Default Email Section
--
-	Footer: the default footer that will be displayed inside every sent mail;


Completed Site Mail
--
The mail that will be sent to the users of the mailing list (your server must support crontab)

-	Sender: mail sender (could be different from the admin)
-	Message: message
“Final mail” uses the default footer.

SEND EMAIL
-
**Contact your subscribed**

Contact every user that is subscribed to your mailing list

-	You can choose if send email to every one or only to selected address
-	Table with subscribed emails
-	Sender(default is admin mail): your email;
-	Object: object of the email;
-	Message: mail message;
-	Use different footer: you can edit your default footer, but the edit will not be saved, basically it’s a  onetime footer

MANAGE SUBSCRIPTION
-
You can delete or modify the subscribed emails

POST NEWS
-
***Post News***
The news section will displayed only if there is at least a news.
The news will be automatically truncated in the frontend if it goes over the 120 characters

-	News Title: the title of the news
-	News: the news’ body
MANAGE NEWS
-
***Manage News***

Here you can edit or delete the news

TRANSLATIONS
-
All the translation files are inside the folder translator/lang and are in csv format, on the left you have the original string and on the right the translated one.
The language is retrieved automatically, but if the translation doesn’t exist the English version will be used.
The file that contains the translated string must be called only with the ISO format (so only the first two letter of the language: `````English -> en.csv  Italian -> it.csv    German-> de.csv`````)
Keep in mind to write the entity name of the needed character that are not part of the simple English keyboard.
Characters Entity Name
For example à is `````&agrave;````` and è is `````&egrave;`````
TROUBLESHOOTING
-
Can’t create database file: ‘Access Denied’
This problem is related to your server files permissions, common settings are:
-	`````File: 644 (0644)`````
-	`````Folders: 755 (0755)`````
If this doesn’t work,open datacheck.php inside admin folder and try to increment them:
-	`````$folderperm=0700;  //Folders Permissions`````
-	`````$fileperm=0644;  //Files Permissions`````
But don’t go further the `````767 (0767)````` permission, otherwise contact your host for further details.

CREDITS
-

-	[Bootstrap](http://getbootstrap.com/2.3.2/) for the responsive style
-	[CKEditor](http://ckeditor.com/) for the textarea customization
-	[DataTables](https://datatables.net/) for the table management
-	[FitText](http://fittextjs.com/) for the frontend message text dimension
-	[Magnific Popup](http://dimsemenov.com/plugins/magnific-popup/) for the frontend modal box
-	[Noty](http://needim.github.io/noty/) for the alert system
-	[SwiftMailer](http://swiftmailer.org/)  for the email system

