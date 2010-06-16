Trinity Framework
=================

This is an experimental web framework for testing new MVC concepts and programming
paradigms.

Concepts
========

The concepts tested in the framework.

True MVC implementation
-----------------------

Typical web frameworks make so many simplifications and changes to the MVC idea
that they eventually get a completely different design pattern. In Trinity,
I try to get back to the origins. Some goals:

1. **Model is not ORM** - in MVC, model serves as an abstract access interface
   to the application logic. The user should not have any dependencies to the
   concrete storage techniques (files, databases, etc.) and should not be aware
   of them. By reducing this layer to ORM, we reduce it to databases which makes
   impossible to use other storage systems in models. In Trinity, ORM lies under
   the model layer.

2. **Views are not templates** - views act similarly to models. They provide an
   abstraction for displaying the data. HTML is only one of possible outputs. The
   other one may be PDF document, which is usually generated with completely
   different techniques. Furthermore, the presentation layer also contains
   some presentation logic. A typical example is list pagination, where we must
   perform some extra calculations in order to determine the current page and
   display the list of available pages. There is no place for it in templates,
   so in many frameworks the pagination code must be written within controllers.
   As a result, we move presentation issues to controllers which should not happen
   in MVC. And does not happen in Trinity.

3. **Controller is not a proxy** - in MVC, views should retrieve the data from models
   directly, using some well-defined interfaces. The only thing the controller
   should do is communicating them one with another, but not serving as a proxy
   between them.

Typical web frameworks implement something that is much closer to Model-View-Presenter
design pattern than to original MVC. My intention is not to claim it is worse
or better, but simply call the things with their real names. So, typical web
frameworks implement MVP, Trinity attempts to test the implementation of MVC
in the web environment.

Event-driven programming
------------------------

According to Wikipedia, event-driven programming is a programming paradigm in
which the flow of the program is determined by events.

Loose dependencies
------------------

Use of external libraries
-------------------------

Installation
============

Project requirements are:

+ PHP 5.3.x
+ [Open Power Libs 2.1](http://www.invenzzia.org/en/projects/open-power-libraries)

Open Power Libs can be obtained from Github (see "OPL" user). In order to install
the project, copy the OPL project directories to the `/lib` directory. Rename
`paths.sample.ini` in the main directory to `paths.ini` and specify the paths
to the library vendors in your filesystem.

The final step is to configure the sample application. Open `/Application/config/area.ini`
and specify the correct host of the default area, where the application will be
accessible from the browser.

Run the application by executing the entry script `index.php`.

Project status
==============

This is an experimental project, not ready for production use. Both the internal
mechanisms and the API **will** change.

License and authors
===================

The source code is available under the New BSD license.

Authors:

+ Tomasz "Zyx" Jędrzejewski <[http://www.zyxist.com/en/](http://www.zyxist.com/en/)>