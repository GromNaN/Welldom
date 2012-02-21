<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="iso-8859-15" omit-xml-declaration="yes" />
<xsl:template match="/">
	<movies>
	<xsl:for-each select="//MovieCatalog/movie">
		<movie><xsl:value-of select="foo:function(title)"/></movie>
	</xsl:for-each>
	</movies>
</xsl:template>
</xsl:stylesheet>
