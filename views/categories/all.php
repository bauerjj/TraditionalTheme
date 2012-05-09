<?php
if (!defined('APPLICATION'))
    exit();
include($this->FetchViewLocation('helper_functions', 'categories'));

$CatList = '';
$DoHeadings = C('Vanilla.Categories.DoHeadings');
$MaxDisplayDepth = C('Vanilla.Categories.MaxDisplayDepth');
$ChildCategories = '';
$this->EventArguments['NumRows'] = $this->CategoryData->NumRows();


if (C('Vanilla.Categories.ShowTabs')) {
    $ViewLocation = Gdn::Controller()->FetchViewLocation('helper_functions', 'Discussions', 'vanilla');
    include_once $ViewLocation;
    WriteFilterTabs($this);
} else {
    ?>


    <?php if (C('Plugin.Traditional.SidePanel', TRUE)) : ?>
        <div id="WithPanel">
        <?php else : ?>
            <div id="NoPanel">
            <?php endif ?>
            <div class="Tabs Headings CategoryHeadings">
                <table class="AllCat TableHeader">
                    <tbody>
                        <tr>
                            <td class="CategoryName"><?php echo T('Forum', 'FORUM'); ?></td>
                            <td class="LatestPost"><?php echo T('Latest Post', 'LATEST POST'); ?></td>
                            <td class="ThreadCount"><?php echo T('Threads', 'THREADS'); ?></td>
                            <td class="PostCount"><?php echo T('Posts', 'POSTS'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <script type="text/javascript">
                $(document).ready(function() {
                    $('.ToggleCat').click(function(){
                        //var Root = $('#WebRoot').val();
                        var CatName = $(this).attr('title');
                        //                        $.ajax({
                        //                            type: 'POST',
                        //                            url: Root + '/discussions/togglecat',
                        //                            data: "CatName="+CatName,
                        //                            success: function(msg){ //alert(msg);
                        //                                $('#'+CatName).toggle();
                        //                        if($(this).hasClass('Minus')){
                        //                            $(this).removeClass('Minus');
                        //                            $(this).addClass('Plus');
                        //                        }
                        //                        else{
                        //                            $(this).removeClass('Plus');
                        //                            $(this).addClass('Minus');
                        //                        }
                        //                            },
                        //                            error: function(msg){ //alert(msg)
                        //                            }
                        //
                        //                        });


                        //If the value exists in the cookie, then that means HIDE the cateogry
                        //If it does not exiss, then that means to EXPAND the category
                        var NewValue = [CatName];
                        var VanillaCat = $.cookie('VanillaCat'); // => 'the_value')
                        if(typeof VanillaCat == 'undefined' || !VanillaCat){
                            //create the first cookie then
                            $.cookie('VanillaCat', NewValue, { expires: 365, path: '/' });
                        }
                        else{
                            VanillaCat = VanillaCat.split(','); //create array
                            if($.inArray(CatName,VanillaCat) != -1) //return -1 if can't find it
                                VanillaCat.splice( $.inArray(CatName,VanillaCat) ,1 ); //if it exsits, remove it
                            else
                                $.merge(VanillaCat, NewValue); //does not exists, hide it now
                            //VanillaCat = $.unique(VanillaCat);
                            $.cookie('VanillaCat', VanillaCat, { expires: 365, path: '/' });
                            //alert(VanillaCat);
                        }

                        //hide/expand
                        if($(this).hasClass('Minus')){
                            $(this).removeClass('Minus');
                            $(this).addClass('Plus');
                        }
                        else{
                            $(this).removeClass('Plus');
                            $(this).addClass('Minus');
                        }
                        $('#'+CatName).toggle();

                    });

                });


            </script>
            <?php
        }
//echo '<ul class="DataList CategoryList' . ($DoHeadings ? ' CategoryListWithHeadings' : '') . '">';
        $Alt = FALSE;
        $Section = FALSE;
        $Count = 0;
//print_r($this->CategoryData->Result()); die;
        foreach ($this->CategoryData->Result() as $Category) {
            //print_r($Category); die;
            $Category->LatestPost = '';
            $this->EventArguments['CatList'] = &$CatList;
            $this->EventArguments['ChildCategories'] = &$ChildCategories;
            $this->EventArguments['Category'] = &$Category;
            $this->FireEvent('BeforeCategoryItem');
            $CssClasses = array(GetValue('Read', $Category) ? 'Read' : 'Unread');
            if (GetValue('Archive', $Category))
                $CssClasses[] = 'Archive';
            if (GetValue('Unfollow', $Category))
                $CssClasses[] = 'Unfollow';
            $CssClasses = implode(' ', $CssClasses);

            $Children = $ChildCategories;

            if ($Category->CategoryID > 0 && $this->SelfUrl == 'categories/all') {
                // If we are below the max depth, and there are some child categories
                // in the $ChildCategories variable, do the replacement.
                if ($Category->Depth == 3 && $ChildCategories != '') {
                    $CatList = str_replace('{ChildCategories}', '<span class="ChildCategories">' . Wrap(T('Child Categories:'), 'b') . ' ' . $ChildCategories . '</span>', $CatList);
                    $ChildCategories = '';
                }

                if ($Category->Depth == 1) { //IMPORTANT:: The little flyout "options" thingy only appears on list items with 'Item' as the class

                    if ($Section == FALSE) {
                        $Section = TRUE;
                    }
                    else
                        echo '</ul>';
                    echo  //'<li class="Category-' . $Category->UrlCode . ' ' . $CssClasses . '">
                    '<div class="CatHeaders">' . Anchor(Gdn_Format::Text($Category->Name), '/categories/' . $Category->UrlCode, 'Title') . '<span class="ToggleCat ' . (HideorExpand($Category->UrlCode) ? 'Plus' : 'Minus') . '" title="' . $Category->UrlCode . '"></span></div>'
                    . '
            ';
                    $Alt = FALSE;



                        echo '<ul class="DataList CategoryList ' .  (HideorExpand($Category->UrlCode) ? 'CatHide' : 'CatExpand') . '" id="' . $Category->UrlCode . '">';

                        if (C('Plugin.Traditional.SubTableHeader', TRUE)) //put this as option
                        echo SubHeader();
                }
                if (($Category->Depth == 2)) {
                    //echo $LastDiscussionTitle;
                    //echo $count++;
                    $Category->Depth = 2; //so CSS plays nice
                    $LastComment = UserBuilder($Category, 'LastComment');
                    $AltCss = $Alt ? ' Alt' : '';
                    $Alt = !$Alt;
                    if ($Children == '</span>')
                        $Children = '';
                    echo '<li class="Item Depth' . $Category->Depth . $AltCss . ' Category-' . $Category->UrlCode . ' ' . $CssClasses . '">
                <table class="AllCat">
                <tbody>
                 <tr>
                    <td class="CategoryName">
                    <span class="CategoryTitle">' . Anchor(Gdn_Format::Text($Category->Name), '/categories/' . $Category->UrlCode, 'Title') . '</span>
                        <span class="CategoryDesc">' . $Category->Description
                    . '</span>
                        ' . $Children . '
                    </td>

                    <td class="LatestPost">' . $Category->LatestPost . '</td>
                    <td class="ThreadCount">' . Gdn_Format::BigNumber($Category->CountAllDiscussions, 'html') . ' </td>
                    <td class="PostCount AllCatListing"> ' . Gdn_Format::BigNumber($Category->CountAllComments, 'html') . GetOptions($Category, $this) . ' </td>
                </tr>
                </tbody>
                </table>
                </li>'
                    ;



                    $CatList .= '
            ';
                }

//        $Count++;
//        if($Count == sizeof($this->CategoryData->Result()))
//                echo '</ul>';
            }
        }

        echo $CatList;
        if (0) //current hack
            echo '</div>';
        ?>
        </ul>

    </div>

    <?php

    function HideorExpand($CatName) {
        if (!empty($_COOKIE['VanillaCat'])) { //this is set inside of Jquery
            $Values = (($_COOKIE['VanillaCat'])); //comma seperated list
            $Values = explode(',', $Values);
            foreach ($Values as $Value) {
                if ($CatName == $Value)
                    return TRUE;
            }
        }
        return FALSE; //not found (don't hide)
    }

    function SubHeader() {
        ob_start();
        ?>
        <li class="SubCategoryHeadings">
            <table class="AllCat SubTableHeader">
                <tbody>
                    <tr>
                        <td class="CategoryName"><?php echo T('Forum', 'FORUM'); ?></td>
                        <td class="LatestPost"><?php echo T('Latest Post', 'LATEST POST'); ?></td>
                        <td class="ThreadCount"><?php echo T('Threads', 'THREADS'); ?></td>
                        <td class="PostCount"><?php echo T('Posts', 'POSTS'); ?></td>
                    </tr>
                </tbody>
            </table>
        </li>
        <?php
        $String = ob_get_contents();
        @ob_end_clean();
        echo $String;
    }

