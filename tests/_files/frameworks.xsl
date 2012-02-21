<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="iso-8859-15" omit-xml-declaration="yes" />
<xsl:template match="/">
	<code>
	<xsl:for-each select="frameworks/framework">
		<framework><xsl:value-of select="name"/></framework> 
	</xsl:for-each>
	</code>
</xsl:template>
</xsl:stylesheet>

