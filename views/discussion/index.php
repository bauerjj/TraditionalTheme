<?php
if (!defined('APPLICATION'))
    exit();

/**
 * ONly change is added view count to title
 * */
//create author
$InsertAuthor = UserBuilder($this->Discussion, 'Insert');
$Permalink = '/discussion/' . $this->Discussion->DiscussionID . '/' . Gdn_Format::Url($this->Discussion->Name) . '/p1';
$Session = Gdn::Session();
$DiscussionName = Gdn_Format::Text($this->Discussion->Name);
if ($DiscussionName == '')
    $DiscussionName = T('Blank Discussion Topic');

$this->EventArguments['DiscussionName'] = &$DiscussionName;
$this->FireEvent('BeforeDiscussionTitle');

if (!function_exists('WriteComment'))
    include($this->FetchViewLocation('helper_functions', 'discussion'));

$PageClass = '';
if ($this->Pager->FirstPage())
    $PageClass = 'FirstPage';
?>
<?php $this->FireEvent('BeforeDiscussion'); ?>
<div class="DiscussionTabs <?php echo $PageClass; ?>">
    <?php
    //move this to handler in plugin
//    if ($Session->IsValid()) {
//        // Bookmark link
//        echo Anchor(
//                '<span>*</span>', '/vanilla/discussion/bookmark/' . $this->Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($this->SelfUrl), 'Bookmark' . ($this->Discussion->Bookmarked == '1' ? ' Bookmarked' : ''), array('title' => T($this->Discussion->Bookmarked == '1' ? 'Unbookmark' : 'Bookmark'))
//        );
//    }
//
    ?>

    <!--    <ul>
            <li><?php
    //don't show this uselss button
//    if (C('Vanilla.Categories.Use') == TRUE) {
//        echo Anchor($this->Discussion->Category, 'categories/' . $this->Discussion->CategoryUrlCode, 'TabLink');
//    } else {
//        echo Anchor(T('All Threads'), 'discussions', 'TabLink');
//    }
    ?></li>
        </ul>-->
    <div class="SubTab">
        <div id="DiscussionInfo">
            <ul>
                <li><span class="DiscussionName"><?php echo $DiscussionName ?></span></li>

                <li>
                    <span class="DiscussionTabDesc">
                    <?php echo sprintf(T('Discussion Info','Discussion in "%s" started by %s, %s'),
                            Anchor($this->Discussion->Category,'categories/' . $this->Discussion->CategoryUrlCode),
                            UserAnchor($InsertAuthor),
                            Anchor(Gdn_Format::Date($this->Discussion->DateInserted), $Permalink)) ?>
                    </span>
                </li>
            </ul>
        </div>
        <div id="DiscussionStats">
            <dl>
                <dt><span class="DiscussionHeaderCountSmall"><?php echo T('View','Views:')?> </span></dt>
                <dd><?php echo Gdn_Format::BigNumber($this->Discussion->CountViews) ?></dd>
            </dl>
            <dl>
                <dt><span class="DiscussionHeaderCountSmall "><?php echo T('Comments','Comments:')?></span></dt>
                    <dd id="Bottom"><?php echo Gdn_Format::BigNumber($this->Discussion->CountComments) ?></dd>
            </dl>
        </div>
    </div>
</div>
<ul class="DataList MessageList Discussion <?php echo $PageClass; ?>">
    <?php echo $this->FetchView('comments'); ?>
</ul>
<?php
$this->FireEvent('AfterDiscussion');
if ($this->Pager->LastPage()) {
    $LastCommentID = $this->AddDefinition('LastCommentID');
    if (!$LastCommentID || $this->Data['Discussion']->LastCommentID > $LastCommentID)
        $this->AddDefinition('LastCommentID', (int) $this->Data['Discussion']->LastCommentID);
    $this->AddDefinition('Vanilla_Comments_AutoRefresh', Gdn::Config('Vanilla.Comments.AutoRefresh', 0));
}
echo '<div class="PageNav">';
echo $this->Pager->ToString('more');
echo '</div>';

// Write out the comment form
if ($this->Discussion->Closed == '1') {
    ?>
    <div class="Foot Closed">
        <div class="Note Closed"><?php echo T('This discussion has been closed.'); ?></div>
        <?php echo Anchor(T('All Threads'), 'discussions', 'TabLink'); ?>
    </div>
    <?php
} else if ($Session->IsValid() && $Session->CheckPermission('Vanilla.Comments.Add', TRUE, 'Category', $this->Discussion->PermissionCategoryID)) {
    echo $this->FetchView('comment', 'post');
} else if ($Session->IsValid()) {
    ?>
    <div class="Foot Closed">
        <div class="Note Closed"><?php echo T('Commenting not allowed.'); ?></div>
        <?php echo Anchor(T('All Threads'), 'discussions', 'TabLink'); ?>
    </div>
    <?php
} else {
    ?>
    <div class="Foot">
        <?php
        echo Anchor(T('Add a Comment'), SignInUrl($this->SelfUrl . (strpos($this->SelfUrl, '?') ? '&' : '?') . 'post#Form_Body'), 'TabLink' . (SignInPopup() ? ' SignInPopup' : ''));
        ?>
    </div>
    <?php
}
?>
