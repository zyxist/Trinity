<?xml version="1.0"?>
<opt:root xmlns:opt="http://xml.invenzzia.org/opt">
<opt:prolog />
<opt:dtd template="html4" />
<html>
	<head>
		<title>Trinity Framework test</title>
	</head>

	<body>
		<h1>This is a Trinity Framework test</h1>

		<opt:section name="content">
			<opt:include from="content" />
		</opt:section>

		<p>{u:entity('copy')} Tomasz Jędrzejewski 2010</p>
	</body>
</html>
</opt:root>