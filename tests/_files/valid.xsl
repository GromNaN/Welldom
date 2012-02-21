<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="iso-8859-15" omit-xml-declaration="yes" />
<xsl:param name="owner" select="owner"/>
<xsl:template match="/">
	<movies>
        <owner><xsl:value-of select="$owner" /></owner>
        <xsl:for-each select="//MovieCatalog/movie">
            <movie><xsl:value-of select="title"/></movie>
        </xsl:for-each>
	</movies>
</xsl:template>
</xsl:stylesheet>
