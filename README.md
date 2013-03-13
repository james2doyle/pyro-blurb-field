pyro-blurb-field
===============

Create small sections with title, image, and body content.

![blurb field creation](http://ohdoylerules.com/wp-content/uploads/2013/03/Screen-Shot-2013-03-13-at-3.38.39-PM.png)

![blurb field on page form](http://ohdoylerules.com/wp-content/uploads/2013/03/Screen-Shot-2013-03-13-at-3.38.21-PM.png)

Features
--------

* Pre-set the number of blurbs/items you want
* Choose the folder for the image dropdown
* Add a title, image and body for each item
* Looping tags to output data
* Includes 0 => None option to allow no image to be set

Usage
-----

``` html
{{ my_field_slug }}
<fieldset id="item{{ id }}">
  <legend>{{ title }}</legend>
  <!-- check and see if the image id is not set to 0 which is none -->
  {{ if image !== 0 }}
  <img src="{{ files:image_url id=image }}">
  {{ endif }}
  <p>{{ body }}</p>
</fieldset>
{{ /my_field_slug }}
```

If there was 3 items, this would output:

``` html
<fieldset id="item1">
  <legend>Item1</legend>
  <img src="http://website.com/files/large/crazyid.jpg">
  <p>Body Content for 1</p>
</fieldset>
<fieldset id="item2">
  <legend>Item2</legend>
  <img src="http://website.com/files/large/crazyid.jpg">
  <p>Body Content for 2</p>
</fieldset>
<fieldset id="item3">
  <legend>Item3</legend>
  <img src="http://website.com/files/large/crazyid.jpg">
  <p>Body Content for 3</p>
</fieldset>
```

To Do
-----

* Add sorting/ordering
* WYSIWYG Toggle

License
-------

(The MIT License)

Copyright (c) 2013 James Doyle <james2doyle@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.