<?xml version="1.0" ?>
<opt:root xmlns:opt="http://xml.invenzzia.org/opt">

	<h3>{$title}</h3>

	<opt:show name="items">
		<table class="list" width="100%" border="1">
			<thead>
				<tr>
					<opt:section name="headers">
					<th>
						<opt:attribute name="str:width" value="$headers.width" opt:if="$headers.width exists"/>
						{$headers.title}
					</th>
					</opt:section>
				</tr>
			</thead>
			<tbody>
				<tr opt:section="items">
				<opt:foreach data="$items" value="val">
					<td>{@val}</td>
				</opt:foreach>
				</tr>
			</tbody>

		</table>
		<opt:else>
			<p>{$noDataMessage}</p>
		</opt:else>
	</opt:show>

</opt:root>