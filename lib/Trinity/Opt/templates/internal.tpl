<?xml version="1.0"?>
<opt:root xmlns:opt="http://xml.invenzzia.org/opt">
<opt:prolog />
<opt:dtd template="xhtml11" />
<html>
  <head>
	<title>Internal Trinity error</title>
  </head>
  <body>
	<h3>Internal Trinity error</h3>
	<p>{$error.message}</p>
	<p>{$error.class}</p>

	{u:$error.backtrace}
  </body>
</html>
</opt:root>