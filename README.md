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

In Trinity, events are fired by various components on different situations. Other
components may listen for the specified event in order to perform some action
when it occurs. Such approach reduces the dependencies and the number of necessary
interfaces.

A nice example is binding the router to the application. In Trinity, the request
broker does not even have to know that there exists such thing, as "router". Router
simply listens for a request creation event. When it is fired, by the broker,
the router executes a tiny, anonymous function which parses the GET arguments and
populates the request object with the extracted data.

Loose dependencies
------------------

One of the primary goals of Trinity is to verify, how loose the dependencies
between the framework components could be, so that the framework could still
work and do its tasks fast. Loose dependencies ease to replace some components
with custom implementation and change the system behaviour.

Loose dependencies are achieved with several techniques:

+ Dependency injection
+ Event-driven programming
+ Service bootstraping mechanism
+ Massive use of external libraries
+ Splitting the framework into several layers

Layer structure
---------------

The framework consists of several layers
built one on top another. For example, in order to build a web application, we
use the `Web` layer which implements the necessary controller and view interfaces
for HTTP environment. However, it does not still provide concrete controllers and
concrete views, because they are provided by another layer: `WebUtils`. If we
do not like the classic two-step *controller - action* layout, we simply abandon
`WebUtils` layer and build our own controller with the interfaces from `Web`.

Elements of contract programming
--------------------------------

This point primarily refers to the communication between views and models. A single
view can be used to render different models, and a single model should work with
several views in different places. With such assumptions, we face a problem, how
to communicate one with another and satisfy these requirements.

A contract is a formal and verificable interface specification and assumption,
what behaviour we expect from it and what benefits we could get. In Trinity,
contracts define various model interfaces. By implementing them in your models,
you are guaranteed to get a certain kind of behaviour from views.

A classic example is a table with some rows from the database provided by the predefined
`Grid` view. We do not have to write all the presentation logic (i.e. pagination,
filtering, sorting) on our own, if we implement the proper interfaces in our models:

+ `Interface_Grid` is the critical interface - it specifies, how the view can
  read the column header titles and the rows from the model.
+ `Interface_Paginable` - after implementing it, our view displays us a complete
  pagination for our list.
+ `Interface_Sortable` - provides the ability to sort the rows by columns.
+ `Interface_Filterable` - provides the ability to filter the data with some criteria.

All the time, we only play at the model level, because if we just implement one
of the interfaces, the view will get us a concrete functionality immediately.

Use of external libraries
-------------------------

Trinity uses lots of external, third party libraries rather than implementing the
certain components in its own fashion. The reason is simple: why to bother with
implementing a custom ORM, if we have great Doctrine? Actually, this framework
is intended to provide the MVC stack and some basic services, such as configuration
handling and sessions. Other issues, like database management, form processing,
template engine should be provided by third-party components and be easily exchangeable.

Installation
============

Project requirements are:

+ PHP 5.3.x
+ [Open Power Libs 2.1](http://www.invenzzia.org/en/projects/open-power-libraries)
+ [Doctrine ORM 2.0](http://www.doctrine-project.org/)
+ APC is recommended

Open Power Libs can be obtained from Github (see "OPL" user). In order to install
the project, copy the OPL project directories to the `/lib` directory. Rename
`paths.sample.ini` in the main directory to `paths.ini` and specify the paths
to the library vendors in your filesystem.

The final step is to configure the sample application. Open `/Application/config/area.ini`
and specify the correct host of the default area, where the application will be
accessible from the browser. Do not forget to create `/Application/cache` directory
if it does not exist.

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