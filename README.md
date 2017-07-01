# pso-registration
Web form for Tethealla PSO registration.

A more secure way for adding new accounts than using the account_add.exe utility.
New users get to sign up with their desired user name and password via their browser.
If the name isn't already taken, nor if the email isn't already associated with an account,
the new account will be inserted using PHP.

Requirements
------------
- mysql-server
- a Tethealla-compliant PSOBB database
- access to said database
- a webserver

Configuration
-------------
There are a few places where you have to change values, like database name and mysql username and stuff.
Those places should be marked with comments.

Installation
------------
I use apache2 on Linux.  That way you can plop it into your /var/www/html folder, rename register.php to index.php,
and you're done already.
I know there are fancier things you can do with this (like embed the register.php into your index.html) but i don't
really know how.
I have no clue how you host this on Windows, but i know it's possible.
