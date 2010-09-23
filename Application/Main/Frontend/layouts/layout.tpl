<?xml version="1.0"?>
<opt:root xmlns:opt="http://xml.invenzzia.org/opt">
<opt:prolog />
<opt:dtd template="html4" />
<html>
	<head>
		<title>Trinity Framework test</title>
		<opt:section name="script" datasource="$helper.script">
			<script type="text/javascript" src="parse:$script.file"></script>
		</opt:section>
		<opt:selector name="style" datasource="$helper.style">
			<opt:file><link rel="stylesheet" type="text/css" href="parse:$style.file" /></opt:file>
			<opt:style><style type="text/css">{u:$style.style}</style></opt:style>
		</opt:selector>
		<!-- OR in this way: -->
		<!-- {u:$helper.style}
		{u:$helper.script} -->
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