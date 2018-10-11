<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php if($this->profile->search_results == 'tagged'):

    $db	=& JFactory::getDBO();
	$query = 'SELECT id FROM #__igallery WHERE parent = 0 ORDER BY ordering LIMIT 1';
	$db->setQuery($query);
	$topCategory = $db->loadObject();
    ?>

    <script type="text/javascript">
    window.addEvent('load', function()
    {
       $('igallery_search').addEvent('submit', function(e)
       {
          new Event(e).stop();
          var tags = document.id('igallery_searchword').get('value');
          var tagsClean = tags.replace(" ", ",");
          var newLocation = 'index.php?option=com_igallery&amp;igid=<?php echo $topCategory->id; ?>&amp;igtype=category&amp;ighidemenu=1&amp;Itemid=<?php echo igUtilityHelper::getItemid($topCategory->id) ?>&amp;igchild=1&amp;&amp;igtags=' + tagsClean;
          window.location = newLocation.replace(/&amp;/g, "&");
       });
    });
    </script>

<?php endif; ?>

<div id="igallery_search<?php echo $this->uniqueid; ?>" class="igallery_search">
	<form action="index.php" method="post" id="igallery_search">
	   <input name="searchword" id="igallery_searchword" maxlength="20" alt="Search" class="inputbox" type="text" size="20" value="<?php echo $this->escape( JText::_('SEARCH_IMAGES') ); ?>..."
       onblur="if(this.value=='') this.value='<?php echo $this->escape( JText::_('SEARCH_IMAGES') ); ?>...';" onfocus="if(this.value=='<?php echo $this->escape( JText::_('SEARCH_IMAGES') ); ?>...') this.value='';" />
	   <input type="hidden" name="option" value="com_search" />
	   <input type="hidden" name="task"   value="search" />
       <input type="hidden" name="areas[0]"   value="igallery" />
       <input type="submit" value="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" class="button" onclick="this.form.searchword.focus();"/>
    </form>
<div class="igallery_clear"></div>
</div>
<div class="igallery_clear"></div>