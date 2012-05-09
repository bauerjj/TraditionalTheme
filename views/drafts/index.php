<?php if (!defined('APPLICATION')) exit();

//Adding in the panel as well as not calling "WriteFilterTabs"


include($this->FetchViewLocation('helper_functions', 'discussions', 'vanilla'));
$Session = Gdn::Session();
$ShowOptions = TRUE;
$Alt = '';
$ViewLocation = $this->FetchViewLocation('drafts', 'drafts');
if ($this->DraftData->NumRows() > 0) {
   echo $this->Pager->ToString('less');
?>
<?php if(C('Plugin.Traditional.SidePanel', TRUE)) : ?>
<div id="WithPanel">
    <?php else : ?>
  <div id="NoPanel">
      <?php endif ?>
<ul class="DataList Drafts">
   <?php
   include($ViewLocation);
   ?>
</ul>
   <?php
   echo $this->Pager->ToString('more');
} else {
   ?>
   <div class="Empty"><?php echo T('You do not have any drafts.'); ?></div>
   <?php
}?>
</div>
