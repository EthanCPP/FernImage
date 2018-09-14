# FernImage
Simple PHP image manipulation class. Allows the "stacking" of multiple image files to produce one image file which can be either downloaded or dislayed on a web page.

To begin, the class must be constructed:

```PHP
$fimg = new FernImage($path, $width, $height);
```
*$path* - location of the "base" layer. Optional.

*$width* - width of the overall image. Optional.

*$height* - height of the overall image. Optional.

You can add as many layers as you wish:

```PHP
$fimg->addLayer($path, $x, $y, $width, $height);
```
*$path* - location of the image file to add to the layer. Optional.

*$x* - number of pixels to the left to place the new image. Optional.

*$y* - number of pixels from the top to place the new image. Optional.

*$width* - width of the new layer. Optional.

*$height* - height of the new layer. Optional.

When you are finished adding layers, the stack must be collapsed:

```PHP
$fimg->collapse();
```

You may now crop the image stack if you wish:

```PHP
$fimg->crop($x, $y, $width, $height);
```
*$x* - pixels from the left to begin the crop. Optional.

*$y* - pixels from the top to begin the crop. Optional.

*$width* - width of the crop. Optional.
	
*$height* - height of the crop. Optional.

Now, to display the image you have two options. You may fetch the image from memory (use this if you plan on running more processes on the image):

```PHP
$fimg->getImage();
```

Or, you can directly draw the image. 

```PHP
$fimg->draw();
```

In case you are directly drawing the image onto the page, I recommend running this script on a separate file (for example **avatar.php**) and then displaying the image on your page with the HTML tag:

```HTML
<img src="avatar.php" />
```
