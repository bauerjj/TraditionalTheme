<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::Session();
include($this->FetchViewLocation('helper_functions', 'discussions', 'vanilla'));
//WriteFilterTabs($this);
if ($this->DiscussionData->NumRows() > 0 || (isset($this->AnnounceData) && is_object($this->AnnounceData) && $this->AnnounceData->NumRows() > 0)) {
?>
<div class="PageNav CatInfo">
                <h1><?php if(isset($this->Category->Name))echo $this->Category->Name ?></h1>
                <div class="CatDescription">
                     <?php echo $this->Pager->ToString('more'); ?>
                    <?php if(isset($this->Category->Description)) echo $this->Category->Description ?>
                </div>
            </div>
            <div style="clear: both"></div>
<ul class="DataList Discussions">
   <?php include($this->FetchViewLocation('discussions')); ?>
</ul>
<?php
   $PagerOptions = array('RecordCount' => $this->Data('CountDiscussions'), 'CurrentRecords' => $this->Data('Discussions')->NumRows());
   if ($this->Data('_PagerUrl')) {
      $PagerOptions['Url'] = $this->Data('_PagerUrl');
   }
   echo PagerModule::Write($PagerOptions);
} else {
   ?>
   <div class="Empty"><?php echo T('No discussions were found.'); ?></div>
   <?php
}
?>