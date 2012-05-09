<?php
if (!defined('APPLICATION'))
    exit();

function WriteDiscussion($Discussion, &$Sender, &$Session, $Alt2) {
    static $Alt = FALSE;

    $Discussion->InsertName = $Discussion->LastName;
    //print_r($Discussion); //die;
    // print_r($Author); //die;
    $CssClass = 'Item';
    $CssClass .= $Discussion->Bookmarked == '1' ? ' Bookmarked' : '';
    $CssClass .= $Alt ? ' Alt ' : '';
    $Alt = !$Alt;
    $CssClass .= $Discussion->Announce == '1' ? ' Announcement' : '';
    $CssClass .= $Discussion->Dismissed == '1' ? ' Dismissed' : '';
    $CssClass .= $Discussion->InsertUserID == $Session->UserID ? ' Mine' : '';
    $CssClass .= ($Discussion->CountUnreadComments > 0 && $Session->IsValid()) ? ' New' : '';
    $DiscussionUrl = '/discussion/' . $Discussion->DiscussionID .
            '/' . Gdn_Format::Url($Discussion->Name) .
            ($Discussion->CountCommentWatch > 0 ? '/#Item_' .
                    $Discussion->CountCommentWatch : ''); //always enable this: C('Vanilla.Comments.AutoOffset')
    $DiscussionUrlStart = '/discussion/' . $Discussion->DiscussionID . '/' . Gdn_Format::Url($Discussion->Name) . '/p1'; //use this to go straight to start of thread
    $Sender->EventArguments['DiscussionUrl'] = &$DiscussionUrl;
    $Sender->EventArguments['Discussion'] = &$Discussion;
    $Sender->EventArguments['CssClass'] = &$CssClass;
    $First = UserBuilder($Discussion, 'First');
    $Last = UserBuilder($Discussion, 'Last');
    $Sender->FireEvent('BeforeDiscussionName');
    $DiscussionName = $Discussion->Name;
    if ($DiscussionName == '')
        $DiscussionName = T('Blank Discussion Topic');

    $Sender->EventArguments['DiscussionName'] = &$DiscussionName;
    static $FirstDiscussion = TRUE;
    if (!$FirstDiscussion)
        $Sender->FireEvent('BetweenDiscussion');
    else
        $FirstDiscussion = FALSE;
    ?>
    <li class="<?php echo $CssClass; ?>">
        <?php
        if (!property_exists($Sender, 'CanEditDiscussions'))
            $Sender->CanEditDiscussions = GetValue('PermsDiscussionsEdit', CategoryModel::Categories($Discussion->CategoryID)) && C('Vanilla.AdminCheckboxes.Use');;
        //$Sender->FireEvent('BeforeDiscussionContent'); //Votes plugin uses this
        if (class_exists('TraditionalPlugin'))
            TraditionalPlugin::CreatePager($Discussion->DiscussionID, $Sender);
        ?>
        <div class="ItemContent Discussion">
            <table class="AllCat">
                <tbody>
                    <tr>
                        <td class="DiscussionsThread">
                            <?php
                            //Now create the "goto last post" image
                            ///@todo there must be a generic function which wraps anchor in image somehwere.....
                            echo Anchor('',(Gdn::Request()->Url($DiscussionUrl, TRUE, FALSE)),array('class'=>'LastPostImage'));
                            ?>
                            <div class="DiscussionsTitle"><?php echo Anchor($DiscussionName, $DiscussionUrlStart, 'Title'); ?></div>
                            <div class="Meta">
                                <?php $Sender->FireEvent('BeforeDiscussionMeta'); ?>
                                <?php if ($Discussion->Announce == '1') { ?>
                                    <span class="Announcement"><?php echo T('Announcement'); ?></span>
                                <?php } ?>
                                <?php if ($Discussion->Closed == '1') { ?>
                                    <span class="Closed"><?php echo T('Closed'); ?></span>
                                <?php } ?>
                            </div>
                            <?php $Sender->FireEvent('AfterDiscussionTitle'); ?> <?php //exmple plugin uses this to echo body ?>
                        </td>
                        <?php
//                if ($Session->IsValid() && $Discussion->CountUnreadComments > 0)
//                    echo '<strong>' . Plural($Discussion->CountUnreadComments, '%s New', '%s New Plural') . '</strong>';
                        $Sender->FireEvent('AfterCountMeta');
                        echo '<td class="DiscussionsOrignalPost">';
                        echo '<div class="DiscussionsImage">';
                        echo UserPhoto($First, array('LinkClass'=>'ProfilePhotoCategory','ImageClass'=>'ProfilePhotoSmall'));
                        echo '</div>';
                        echo '<div class="DiscussionsMeta">';
                        echo Wrap(UserAnchor($First), 'span', array('class' => 'LastCommentBy'));
                        echo '<br/>';
                        echo '<span class="LastCommentDate">' . Gdn_Format::Date($Discussion->LastDate) . '</span>';
                        echo '</div>';
                        echo '</td>';

                        echo '<td class="DiscussionsLatestPost">';
                        echo '<div class="DiscussionsImage">';
                        if ($Discussion->LastCommentID != '') { //are there any posts to this thread?
                            //yes
                            echo UserPhoto($Last, array('LinkClass'=>'ProfilePhotoCategory','ImageClass'=>'ProfilePhotoSmall'));
                            echo '</div>';
                            echo '<div class="DiscussionsMeta">';
                            echo Wrap(UserAnchor($Last), 'span', array('class' => 'LastCommentBy'));
                            echo '<br/>';
                            echo '<span class="LastCommentDate">' . Gdn_Format::Date($Discussion->LastDate) . '</span>';
                            echo '</div>';
                        } else {
                            //no...use original poster info
                            echo UserPhoto($First, array('LinkClass'=>'ProfilePhotoCategory','ImageClass'=>'ProfilePhotoSmall'));
                            echo '</div>';
                            echo '<div class="DiscussionsMeta">';
                            echo Wrap(UserAnchor($First), 'span', array('class' => 'LastCommentBy'));
                            echo '<br/>';
                            echo '<span class="LastCommentDate">' . Gdn_Format::Date($Discussion->LastDate) . '</span>';
                            echo '</div>';

//                            if ($Source = GetValue('Source', $Discussion)) {
//                                echo ' ' . sprintf(T('via %s'), T($Source . ' Source', $Source));
//                            }
//
//                            echo '</span>';
                        }
                        echo '</td>';
//                        if (C('Vanilla.Categories.Use') && $Discussion->CategoryUrlCode != '') //this echos out current category
//                            echo Wrap(Anchor($Discussion->Category, '/categories/' . rawurlencode($Discussion->CategoryUrlCode), 'Category'));
                        ?>
                        <td class="DiscussionsReplies">
                            <span class="CommentCount"><?php echo Gdn_Format::BigNumber($Discussion->CountComments- 1) ?></span>
                        </td>
                        <td class="DiscussionsViews">
                            <span class="CommentCount"><?php echo Gdn_Format::BigNumber($Discussion->CountViews - 1) ?></span>
                        </td>
                        <td class="DiscussionsBookmark">
                            <?php
                            $Title = T($Discussion->Bookmarked == '1' ? 'Unbookmark' : 'Bookmark');
                            echo Anchor(
                                    '<span class="Star">'
                                    . Img('applications/dashboard/design/images/pixel.png', array('alt' => $Title))
                                    . '</span>', '/vanilla/discussion/bookmark/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'Bookmark' . ($Discussion->Bookmarked == '1' ? ' Bookmarked' : ''), array('title' => $Title)
                            );
                            ?>
                            <?php echo WriteOptions($Discussion, $Sender, $Session); //bring ups the little "options" flyout?>
                        </td>

                        <?php
                        $Sender->FireEvent('DiscussionMeta');
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </li>
    <?php
}

/**
 * This writes the "Popular" "My Drafts" etc at the top -JJB
 * @param object $Sender
 */
function WriteFilterTabs(&$Sender) {
    $Session = Gdn::Session();
    $Title = property_exists($Sender, 'Category') ? GetValue('Name', $Sender->Category, '') : '';
    if ($Title == '')
        $Title = T('All Threads');

    $Bookmarked = T('My Bookmarks');
    $MyDiscussions = T('My Threads');
    $MyDrafts = T('My Drafts');
    $CountBookmarks = 0;
    $CountDiscussions = 0;
    $CountDrafts = 0;


    if ($Session->IsValid()) {
        $CountBookmarks = $Session->User->CountBookmarks;
        $CountDiscussions = $Session->User->CountDiscussions;
        $CountDrafts = $Session->User->CountDrafts;
    }
    if ($CountBookmarks === NULL) {
        $Bookmarked .= '<span class="Popin" rel="' . Url('/discussions/UserBookmarkCount') . '">-</span>';
    } elseif (is_numeric($CountBookmarks) && $CountBookmarks > 0)
        $Bookmarked .= '<span>' . $CountBookmarks . '</span>';

    if (is_numeric($CountDiscussions) && $CountDiscussions > 0)
        $MyDiscussions .= '<span>' . $CountDiscussions . '</span>';

    if (is_numeric($CountDrafts) && $CountDrafts > 0)
        $MyDrafts .= '<span>' . $CountDrafts . '</span>';
    ?>
        <ul class="DiscussionsInfo">
            <?php $Sender->FireEvent('BeforeDiscussionTabs'); ?>

            <?php
            if (C('Vanilla.Categories.ShowTabs')) {
                $CssClass = '';
                if (strtolower($Sender->ControllerName) == 'categoriescontroller' && strtolower($Sender->RequestMethod) == 'all') {
                    $CssClass = 'Active';
                }

                echo "<li class=\"$CssClass\">" . Anchor(T('Categories'), '/categories/all', '') . '</li>';
            }
            ?>
            <?php if ($CountBookmarks > 0 || $Sender->RequestMethod == 'bookmarked') { ?>
                <li<?php echo $Sender->RequestMethod == 'bookmarked' ? ' class="Active"' : ''; ?>><?php echo Anchor($Bookmarked, '/discussions/bookmarked', 'MyBookmarks'); ?></li>
                <?php
                $Sender->FireEvent('AfterBookmarksTab');
            }
            if ($CountDiscussions > 0 || $Sender->RequestMethod == 'mine') {
                ?>
                <li<?php echo $Sender->RequestMethod == 'mine' ? ' class="Active"' : ''; ?>><?php echo Anchor($MyDiscussions, '/discussions/mine', 'MyDiscussions'); ?></li>
                <?php
            }
            if ($CountDrafts > 0 || $Sender->ControllerName == 'draftscontroller') {
                ?>
                <li<?php echo $Sender->ControllerName == 'draftscontroller' ? ' class="Active"' : ''; ?>><?php echo Anchor($MyDrafts, '/drafts', 'MyDrafts'); ?></li>
                <?php
            }
            $Sender->FireEvent('AfterDiscussionTabs');
            ?>
        </ul>
        <?php
        if (!property_exists($Sender, 'CanEditDiscussions'))
            $Sender->CanEditDiscussions = $Session->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', 'any') && C('Vanilla.AdminCheckboxes.Use');

        if ($Sender->CanEditDiscussions) {
            ?>
            <span class="AdminCheck">
                <input type="checkbox" name="Toggle" />
            </span>
    <?php } ?>
    <?php
}

/**
 * Render options that the user has for this discussion.
 */
function WriteOptions($Discussion, &$Sender, &$Session) {
    if ($Session->IsValid() && $Sender->ShowOptions) {
        echo '<div class="Options">';
        $Sender->Options = '';

        // Dismiss an announcement
        if (C('Vanilla.Discussions.Dismiss', 1) && $Discussion->Announce == '1' && $Discussion->Dismissed != '1')
            $Sender->Options .= '<li>' . Anchor(T('Dismiss'), 'vanilla/discussion/dismissannouncement/' . $Discussion->DiscussionID . '/' . $Session->TransientKey(), 'DismissAnnouncement') . '</li>';

        // Edit discussion
        if ($Discussion->FirstUserID == $Session->UserID || $Session->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', $Discussion->PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(T('Edit'), 'vanilla/post/editdiscussion/' . $Discussion->DiscussionID, 'EditDiscussion') . '</li>';

        // Announce discussion
        if ($Session->CheckPermission('Vanilla.Discussions.Announce', TRUE, 'Category', $Discussion->PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(T($Discussion->Announce == '1' ? 'Unannounce' : 'Announce'), 'vanilla/discussion/announce/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'AnnounceDiscussion') . '</li>';

        // Sink discussion
        if ($Session->CheckPermission('Vanilla.Discussions.Sink', TRUE, 'Category', $Discussion->PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(T($Discussion->Sink == '1' ? 'Unsink' : 'Sink'), 'vanilla/discussion/sink/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'SinkDiscussion') . '</li>';

        // Close discussion
        if ($Session->CheckPermission('Vanilla.Discussions.Close', TRUE, 'Category', $Discussion->PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(T($Discussion->Closed == '1' ? 'Reopen' : 'Close'), 'vanilla/discussion/close/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'CloseDiscussion') . '</li>';

        // Delete discussion
        if ($Session->CheckPermission('Vanilla.Discussions.Delete', TRUE, 'Category', $Discussion->PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(T('Delete'), 'vanilla/discussion/delete/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'DeleteDiscussion') . '</li>';

        // Allow plugins to add options
        $Sender->FireEvent('DiscussionOptions');

        if ($Sender->Options != '') {
            ?>
            <div class="ToggleFlyout OptionsMenu">
                <div class="MenuTitle"><?php echo T('Options'); ?></div>
                <ul class="Flyout MenuItems">
            <?php echo $Sender->Options; ?>
                </ul>
            </div>
            <?php
        }
        // Admin check.
        if ($Sender->CanEditDiscussions) {
            if (!property_exists($Sender, 'CheckedDiscussions')) {
                $Sender->CheckedDiscussions = (array) $Session->GetAttribute('CheckedDiscussions', array());
                if (!is_array($Sender->CheckedDiscussions))
                    $Sender->CheckedDiscussions = array();
            }

            $ItemSelected = in_array($Discussion->DiscussionID, $Sender->CheckedDiscussions);
            echo '<span class="AdminCheck"><input type="checkbox" name="DiscussionID[]" value="' . $Discussion->DiscussionID . '"' . ($ItemSelected ? ' checked="checked"' : '') . ' /></span>';
        }

        // Bookmark link
//        $Title = T($Discussion->Bookmarked == '1' ? 'Unbookmark' : 'Bookmark');
//        echo Anchor(
//                '<span class="Star">'
//                . Img('applications/dashboard/design/images/pixel.png', array('alt' => $Title))
//                . '</span>', '/vanilla/discussion/bookmark/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'Bookmark' . ($Discussion->Bookmarked == '1' ? ' Bookmarked' : ''), array('title' => $Title)
//        );

        echo '</div>';
    }
}

