<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE xsl:stylesheet [<!ENTITY nbsp "&#160;">]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
<!-- need to add after 1st line of riskinfo.xml
	<?xml-stylesheet type="text/xsl" href="Risks.xsl"?>
 or use javascript in RiskInfo.html
-->
<!-- /i3x5 root node -->
<xsl:template match="/i3x5">
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="3x5.css"/>
<script type="text/javascript" src="3x5.js"></script>
<script type="text/javascript" src="sorttable.js"></script>
<title>
3x5 Card Database
</title>
</head>
<body class="main">
<xsl:apply-templates select="settings"/>
<xsl:apply-templates select="xbatches"/>
</body>
</html>
</xsl:template>

<!-- /i3x5/settings node (for debugging)-->
<xsl:template match="settings">
<table>
<tr>
<xml:for-each select="dates">
	<td><xsl:value-of select="@type"/></td>
	<td><xsl:value-of select="@selected"/></td>
</xml:for-each>
</tr>
<tr>
<xml:for-each select="key">
	<td><xsl:value-of select="@type"/></td>
	<td><xsl:value-of select="@selected"/></td>
</xml:for-each>
</tr>
</table>
</xsl:template>

<!-- /i3x5/xbatches node -->
<xsl:template match="xbatches">
<!-- global (or batch) changes -->
<form method="post">
<table width="100%" border="1" class="sortable">
<xsl:apply-templates select="xbatch"/>
</table>
</form>
</xsl:template>

<!-- /i3x5/xbatches/xbatch node -->
<xsl:template match="xbatch">
<tr>
  <th colspan="3" width="50%" align="left" class="h_batch">
    <xsl:value-of select="batch"/>
  </th><th colspan="3" width="50%" align="right" class="h_batch">
    <xsl:value-of select="card"/>
</th></tr>
<tr><th colspan="2" width="15%" class="h_id"> card id </th>
  <th width="20%" class="h_number"> <xsl:value-of select="num"/> </th>
  <th width="63%" class="h_title"> <xsl:value-of select="title"/> </th>
  <th width="1%" class="h_cdate"> C </th>
  <th width="1%" class="h_mdate"> M </th>
</tr>
<xsl:apply-templates select="xcards"/>
</xsl:template>

<!-- /i3x5/xbatches/xbatch/xcards node -->
<xsl:template match="xcards">
<xsl:apply-templates select="xcard"/>
</xsl:template>

<!-- /i3x5/xbatches/xbatch/xcards/xcard node -->
<xsl:template match="xcard">
<xsl:variable name="id" select="id"/>
<xsl:variable name="cid" select="concat('b',bid,'_c',id)"/>
<xsl:variable name="num" select="num"/>
<xsl:variable name="titlex" select="titlex"/>
<xsl:variable name="c_date" select="createdate"/>
<xsl:variable name="m_date" select="moddate"/>
<tr card_id="{$id}" card_number="{$num}" card_title="{$titlex}" card_cdate="{$c_date}" card_mdate="{$m_date}">
  <td colspan="1" width="2%" class="b_check">
  <span class="nonprint">
  <input type="checkbox" name="c_check[{$id}]" value="true"/>
  X
  </span>
  </td>
  <td colspan="1" width="13%" class="b_id">
  <a href="javascript:showcard('{$cid}');">
  <xsl:value-of select="$id"/>
    <xsl:if test="rid&gt;0">
	(<xsl:value-of select="rid"/>)
    </xsl:if>
  </a>
</td>
<td width="20%" class="b_number">
  <xsl:value-of select="num"/></td>
<td colspan="3" width="65%" class="b_title"><xsl:value-of select="title"/></td>
<td colspan="1" width="0%" class="b_cdate hidden"></td>
<td colspan="1" width="0%" class="b_mdate hidden"></td>
</tr>
<tr id="{$cid}" class="hidden"><td colspan="4" width="100%">
  <table width="100%">
    <tr><td colspan="2" width="100%" class="b_card">
      <xsl:if test="formatted='t'">
	<pre><xsl:value-of select="card"/></pre>
      </xsl:if>
      <xsl:if test="formatted!='t'">
	<xsl:call-template name="nl2br">
	<xsl:with-param name="contents" select="card" />
	</xsl:call-template>
      </xsl:if>
    </td></tr>
    <tr><td align="center">created:&nbsp;<xsl:value-of select="cdate"/></td>
        <td align="center">modified:&nbsp;<xsl:value-of select="mdate"/></td>
    </tr>
  </table>
</td></tr>
</xsl:template>

<!-- function to replace newlines with <br/> in card -->
<!--   from Melvyn Sopacua IDG.nl via google search -->
<xsl:template name="nl2br">
  <xsl:param name="contents" />
  <xsl:choose>
    <xsl:when test="contains($contents, '&#10;')">
    <xsl:value-of select="substring-before($contents, '&#10;')" />
    <br class="card_br"/>
    <xsl:call-template name="nl2br">
    <xsl:with-param name="contents" select="substring-after($contents, '&#10;')" />
    </xsl:call-template>
  </xsl:when>
  <xsl:otherwise>
    <xsl:value-of select="$contents" />
  </xsl:otherwise>
  </xsl:choose>
</xsl:template>

</xsl:stylesheet>
