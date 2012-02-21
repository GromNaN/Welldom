<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:param name="month"></xsl:param>
<xsl:param name="year"></xsl:param>
<xsl:template match="/">
	<code>
	<xsl:for-each select="frameworks/framework">
		<framework><xsl:value-of select="name"/></framework> 
	</xsl:for-each>
    <year><xsl:value-of select="$year"></xsl:value-of></year>
    <month><xsl:value-of select="$month"></xsl:value-of></month>
	</code>
</xsl:template>
</xsl:stylesheet>

