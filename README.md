# SuperSimpleGallery
Super Simple PHP Photo Gallery

WARNING, Mispelled words ahead!

Extrmely simplified photo gallery with image management function.  I may further develop this in the future, but for now it very simple in what it does.  See *gallery.php* for an example front end implimentation.

The backend management is where most of the work is done.

1. *edit.php* is password protected, change the user name and password as required by editing the file's contents.
2. All paths assume that everything is stored in the same directory, including images.  Like I said, I may develop this further if the interest is their.
3. Only JPEG is supported for now, but hte frame work exists to support PNG and GIF.
4. You need mod_rewrite in order to use the thmbnailing feature.  See the included .htaccess file.

The backend management interface if self explanatory, but here is the quick start:

    There are three sections, called "Upload new Image", "Current Active Images", and "Deleted Items".
    To upload a new image, under "Upload new Image", click "browse", select the file from your computer, then click "save image".  It will be added to the end of "Current Active Images".
    Images are given 'points' (the number on the top left) that determines the order in which they appear. 
    To re-order the images, use the arrows to increment and decrement the image numbers.  This may be hard to follow at first, but to summarize:
        Double Left - Decrease by 5 points.
        Double Right - Increases  by 5 points.
        Single Right/Left - Increases/Decreases by 1 point.
    To delete an image, click the trash icon.
    To restore, click the restore icon under "Deleted Items".

Hint 1: You have 3 images: 260, 270, 280.  You want to move 270 before 260.  You must decrease 270 to 259, OR increase 260 to 271.

Hint 2: You have an image with a number 261 and another with 262.  If you want to insert another image between 261 and 262, you need to either decrease 261 by 1 point OR increase 262 by 1 point.