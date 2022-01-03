TRUNCATE TABLE i3x5_help;

INSERT INTO i3x5_help (key,help) VALUES ( 'login','
You must login as some user.  If you don''t know of an existing user
then create a new one.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'login username','
The "username" identifies the project, and is unique with respect to
all other project usernames in the 3x5 card database.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'login password','
The "password" given to you by the administrator
determines what level of access you have to the project.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'login crypt','
The "Encrypt Key" is given to you by the administrator
for encrypting/decrypting those cards and batches which
require encrypted confidentiality.
This string is _not_ stored in the 3x5 database, but is only
available to this session.
You can skip the key at login and input it later (with a hint).
');

INSERT INTO i3x5_help (key,help) VALUES ( 'hint crypt','
The project admin is the only one that can set the crypt key.
They should have also provided a hint descriptive enough to
remind the knowledgeable user of what it may be, but obtuse enough
as to not give it away to anyone else.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'logout','
Logout as this user, then you can re-login as the same
user with a different password (and access level)
or login as a different user (and project).
');

INSERT INTO i3x5_help (key,help) VALUES ( 'unknown','
Keyword does not exist in the help database ... This is an error!
Notify the administrator of the specific conditions.');

INSERT INTO i3x5_help (key,help) VALUES ( 'create user','
The user name needs to be unique to all other usernames
in the 3x5 card database.  Enter all of the project values.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'update user','
Update all of the project values except for the username.
Passwords are encrypted and only viewable here.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'username','
The "username" that identifies the project.
Must be unique with respect to all other projects in the 3x5 card database.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'project','
The project name is the descriptive title for the project.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'projcrypt','
The "Project Encrypt Key" is used by the 3x5 database application
for encrypting/decrypting those cards and batches which
require encrypted confidentiality.
However this string is _not_ stored in the 3x5 database, but must
be given by the user when logging in to a session.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'projcrypthint','
This hint should relate to the "Project Encrypt Key", but not
give it out directly.  So it needs to be descriptive enough
for your knowledgeable user to remember what it should be,
but not easy enough for someone else to guess it.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'passwd_root','
The root password for the 3x5 database ...
DO NOT FORGET THIS PASSWORD!
The root administrator has all power over ALL cards and batches.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'passwd_admin','
The administrator password for this project ...
DO NOT FORGET THIS PASSWORD!
The administrator can move / relate / delete cards and batches.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'passwd_w','
The write password gives the user privilege to enter and edit
data directly in to the number, title, card fields,
and can insert cards.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'passwd_a','
The append password gives the user only the privilege
of appending data to existing cards, and inserting cards.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'passwd_r','
The read password gives the user only the privilege
of reading the card data.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'author','
The project administrator,
the person to contact if the project cards are to
be dropped for lack of use.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'email','
The project administrator''s email address.
Please give a valid email address otherwise 
you will not be notified of pending changes
(or forgotten admin passwords)!
');

INSERT INTO i3x5_help (key,help) VALUES ( 'challenge','
Give a question that only you can answer.  Such as:
"Your childhood pets name", "Mothers maiden name",
"Last 4 digits of you social security number", etc.
You must answer this question if you forget your password.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'response','
The "correct" answer for the "challenge" question.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'Admin','
The administrator can move / relate / delete cards or batches
and change batch properties.
The administrator is all powerful with respect to the project.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'Write','
The write privilege allows the entering and editing of data directly in to
the number, title, card fields, and allows inserting of new cards.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'Append-Only','
The append privilege allows only the appending of data to existing cards,
and the inserting of new cards.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'Read-Only','
The read privilege allows only the reading of existing card data.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch properties','
Batch Properties refers to the labels given to the
number, title, and card fields.  Choose one to view, or create
a new batch.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'create batch','
Must give a batch name that is unique to the project.
Also give the batch properties for the number, title, and card fields.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'update batch','
Can change the batch properties for the number ,title, card fields.
The  batch name may be changed, but must be unique to the project.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch copy','
Copy the properties from the selected batch to this batch.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch relate','
Relate the properties for this batch to the selected batch.
Any changes to that batch''s properties
will also be reflected in this batch.
If relating to a batch that is related to another ... this batch
will just be related to the root (non-related) batch.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch relation','
The properties for those batches with entries below
are related to the indicated batch.
Any changes to that batch''s properties
are reflected in the corresponding batch.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch count','
The number of cards in this batch.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'update all cards','
All the card contents (except for the relation cards) can be updated
collectively by, first changing the card contents for all those desired,
and then selecting the "Update All Cards" button.  Note that any
"Batch Submit" check box selections will be lost and can not be processed
at the same time.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'update relation','
Only cards that are not related to other cards can be updated
in a collective operation.  The reason for this is that the
order of cards dictates what changes will be implemented
and there would be no way to resolve conflicts.  Hence,
only the point of truth, the original card can be updated
in a collective fashion.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch ops','
Select a collective or batch operation at the right, then
check the boxes in front of the card id numbers for which the collective
operation will be applied against.  Note that any card contents that have
been changed will not be updated with the "Batch Submit"
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch name','
The name for this batch, which must be unique
within the project.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch number','
The label for the batch card number.
If blank, this field will not be shown.
Sorting can be performed on either the number or the title.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch title','
The label for the batch card title.
If blank, this field will not be shown.
Sorting can be performed on either the number or the title.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch card','
The label for the batch card.
If blank, this field will not be shown (which is somewhat pointless).
The card carries the essential data.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'card example','
The typical batch properties (field names) you may want
for general purpose 3x5 cards.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'journal example','
The typical batch properties (field names) you may want
for keeping a daily journal.
The date should should be in the YYYYMMDD format for proper sorting.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'people example','
The typical batch properties (field names) you may want
for tracking a list of people.
The name could be last,first for proper sorting.
Usually the card number is unimportant.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'recipe example','
The recipes can be sorted by calories per serving or by creating
different batches that classify the recipe (e.g. beef, poultry, dessert,
etc.).
');

INSERT INTO i3x5_help (key,help) VALUES ( 'list batches','
This lists the batches for this project (or user).
');

INSERT INTO i3x5_help (key,help) VALUES ( 'delete batch','
Delete a whole batch, except for those cards that are linked to
from other batches.  If there are linked cards then you will
need to dispose of the cards individually.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch id','
The batch id is an internal identifier for the batch
and is never used directly by the user,
but is useful for determining relations.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch property','
These the are the essential card properties defined for each batch.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch label','
The labels are displayed for each field ... they should be short
but descriptive.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch help','
Helpful and explanatory text for the given label or field.
This text is generally shown by clicking on the label.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'batch select','
Select one or more batches to view or edit.
The _ALL_ "batch" will select all the batches.
The _NOT_ "batch" will toggle the current selection.
Click on the "Check" button to see the selection result.
When satisfied with the selection then click on the "View"
button.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'search select','
Search all the cards associated with this project.
A simple or advanced search can be performed.
The simple search will find the given text string
in the title or card.
The advance search gives more options and uses
POSIX regular expressions.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'view attributes','
Card viewing attributes select whether to view or edit the
cards, view the full card or not, and how to sort, group, or
arrange the cards.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'view cards','
Select whether to edit the cards, by selecting "Edit", or to
view the cards with "List".  This can be overruled by clicking on
View or Edit links.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'view show','
Select whether to include the "card" body when viewing
or editing.
Selecting "Header Only" will only show the "number" and "title"
fields.  Selecting "Full" will show the "card" body too.
Viewing or hiding the card can be toggled by clicking the labeled
button.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'view dates','
Select whether to show the "card" creation and modification dates
');

INSERT INTO i3x5_help (key,help) VALUES ( 'view key','
Select which field to sort the cards by.
Sorting can be done on the "number" (numerically),
"title" (alphabetically), the creation date, or the modification date
fields.  (If a date field is selected the sort will be done even
if the date fields are not shown.)
');

INSERT INTO i3x5_help (key,help) VALUES ( 'view order','
Select whether to order the cards with the given key in an
ascending or descending fashion.
When descending the higher values or dates are near the top.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'view group','
Selecting "Batch" will maintain the sorted cards in the selected batches
with each batch being identified,
else selecting "None" then the entire collection of cards
in the selected batches will be sorted together and no
batch designation will be maintained.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'card view','
This is where the cards are edited or viewed given the
card attributes selected prior.<br/>
If editing, then the first card is an insert card where the batch
properties shown reflect the first batch, but the actual batch
the card belongs to is given by the batch selector.<br/>
All cards can be moved, copied, or related to other batches (the
original batch is shown by default), otherwise the fields can be
edited.  Cards can be updated individually or collectively (with
some restrictions).<br/>
Be aware of fields with differing colors
these usually denotes an exceptional condition.<br/>
Listing cards creates a more printer friendly web page
');

INSERT INTO i3x5_help (key,help) VALUES ( 'csv file','
Comma Separated Values - a common ASCII flat-file format.<br/>
For lack of a authoritative definition.  The following will be used.
Each field is followed by a comma except the last in a record.
Each record is followed by a newline.  Values are double quoted if they
contain commas(,), double quotes ("), or newlines (\\n).<br/>

Values which contain double quotes (") each are expanded to
a pair of double quotes ("").<br/>
Values which contain embedded newlines are reduced to the (\\n) characters,
and it is up to the importing program to handle them correctly.<br/>
The first line usually has the field names, which will be defined
by the first card''s batch field names.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'encrypted','
The given card will need to be encrypted using the user project
encryption key.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'formatted','
Display the given card as <TT>fixed width formatted</TT>
when listed.  (Have it look like the "edited" version when listed.)
');

INSERT INTO i3x5_help (key,help) VALUES ( 'sql comp','
IS : equal to the given value (synonymous with "=")<br/>
NOT : not equal to given value (synonymous with "!=")<br/>
LIKE : _ match any single character, % matches any number of characters<br/>
ILIKE: ignore case LIKE<br/>
SIMILAR TO: a cross of LIKE and regex pattern-matching<br/>
~  : POSIX regex pattern-matching<br/>
~* : ignore case ~<br/>
!  : NOT for above<br/>
IS [NOT] NULL: whether data field is NULL or not
<a href="https://www.postgresql.org/docs/current/functions-matching.html" target="_blank">PostgreSQL docs</a>.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'sql and or','
AND statements are collected first and grouped together<br/>
OR statements are then added to the end.<br/>
NOT negates the given statement.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'sql between','
If the second value is given then the search will assume a BETWEEN
search for values between the first and second given values inclusive.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'keyword search','
This simple search just looks for the given keyword or character string within
the card title or card body.  Check whether to make the search
case sensitive or not.  The case insensitive search is default.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'advanced search','
Leave fields blank that are not to be considered in the advanced search.
The AND statements are collected first and grouped together
followed by the OR statements.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'change role','
The project Admin can try out the lower levels of access.
Just select "Change Role" again to restore to the admin level.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'bid','
The Batch ID (bid) is assigned by 3x5 and is unique to each batch of cards.
The attributes can be related to those of another batch.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'cid','
The Card ID (cid) is assigned by 3x5 and is unique to each card whether
a data card or a related card.  However, searches can only be performed
for the source card (i.e. not a card related to a source card).
');

INSERT INTO i3x5_help (key,help) VALUES ( 'number','
The NUMBER field is one of the three fields a card creator can use
to identify a given card.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'title','
The TITLE field is one of the three fields a card creator can use
to identify a given card, and is a simple text string.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'card','
The CARD field is one of the three fields a card creator can use
to define a given card, and is usually the bulk data field which
can be formatted with HTML or fixed-width characters.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'createdate','
The creation date and timestamp when a card was created (in the timezone
defined for the database).  This date is never updated for a card.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'moddate','
The modification date and timestamp when a card was last modified
(in the timezone defined for the database).  A card never updated
will have the same value as the creation date.
');

INSERT INTO i3x5_help (key,help) VALUES ( 'xxx','
');
