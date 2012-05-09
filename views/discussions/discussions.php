<?php
if (!defined('APPLICATION'))
    exit();
$Session = Gdn::Session();
?>
<?php if (C('Plugin.Traditional.SidePanel', TRUE) && $this->ControllerName != 'profilecontroller' && (strpos($this->SelfUrl,'profile/discussions') === FALSE)) : //notice 3 equals here....for clicking 'more discussins' turns into using the discussions controller'?>
    <div id="WithPanel">
    <?php else : ?>
        <div id="NoPanel">
        <?php endif ?>

        <?php if ($this->ControllerName != 'profilecontroller' && (strpos($this->SelfUrl,'profile/discussions') === FALSE)): //notice 3 equals here....so the profile when viwing user discussions only loads this header once!!! ?>
            <div class="Tabs Headings CategoryHeadings">
                <table class="AllCat TableHeader">
                    <tbody>
                        <tr>
                            <td class="DiscussionsThread"><?php echo T('Thread', 'THREAD'); ?></td>
                            <td class="DiscussionsOrignalPost"><?php echo T('Orginal Post', 'ORIGINAL POST'); ?></td>
                            <td class="DiscussionsLatestPost"><?php echo T('Latest Post', 'LATEST POST'); ?></td>
                            <td class="DiscussionsReplies"><?php echo T('Replies', 'REPLIES'); ?></td>
                            <td class="DiscussionsViews"><?php echo T('Views', 'VIEWS'); ?></td>
                            <td class="DiscussionsBookmark"><?php echo T(''); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
        <?php
        if (!function_exists('WriteDiscussion'))
            include($this->FetchViewLocation('helper_functions', 'discussions', 'vanilla'));


        $Alt = '';
        if (property_exists($this, 'AnnounceData') && is_object($this->AnnounceData)) {
            foreach ($this->AnnounceData->Result() as $Discussion) {
                $Alt = $Alt == ' Alt' ? '' : ' Alt';
                WriteDiscussion($Discussion, $this, $Session, $Alt);
            }
        }

        $Alt = '';
        foreach ($this->DiscussionData->Result() as $Discussion) {
            $Alt = $Alt == ' Alt' ? '' : ' Alt';
            WriteDiscussion($Discussion, $this, $Session, $Alt);
        }
        ?>
        <?php if ($this->ControllerName != 'profilecontroller'): ?>
        </div>
    <?php endif ?>
    <script type="text/javascript">

        $(document).ready(function() {

            $(".GotoPageLink").click(function(){
                var id=$(this).attr('name');

                $('#'+id).toggle();
                //alert(id);


            });
        });


    </script>