<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<!--[if IE 6]>
<style>
/* This is so the sidepanel in IE6 does not get cut off...god damn IE6 */
.AllCat {
    width: 0% !important;
}
</style>
<![endif]-->


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-ca">
    <head>
        <?php $this->RenderAsset('Head'); ?>

       <?php  if(C('Plugin.Traditional.SidePanel', FALSE))
               $Style = '#mainContainer{
    float: left;
    margin-right: -260px;
    width: 100%;

}
#WithPanel{
    margin-right: 260px;
};';
       else $Style = '#mainContainer{
    float: left;
    margin-right: 0;
    width: 100%;

}
#WithPanel{
    margin-right: 0px;
};';



               ?>
       <?php echo ' <style type="text/css">
            #Body{ width: '.C("Plugin.Traditional.BodyWidth","90%").' }



         </style> '; ?>
    </head>
        <body id="<?php echo $BodyIdentifier; ?>" class="<?php echo $this->CssClass;?>">
            <div id="Frame">
            <div id="Head">
                <?php
                $Session = Gdn::Session();
                $Authenticator = Gdn::Authenticator();
                if (!$Session->IsValid()) {
                    $this->Menu->HtmlId  = '';
                    if ($this->Menu) {
                        $Attribs = array();
                        if (C('Garden.SignIn.Popup') && strpos(Gdn::Request()->Url(), 'entry') === FALSE)
                            $Attribs['class'] = 'SignInPopup';


                        echo '<div id="LoginBar">';
                        echo '<h3 id="LoginHandle">' . Anchor(T('Log in or Sign up'), $Authenticator->SignInUrl($this->SelfUrl), FALSE, array('class' => 'SignInPopup'), $Attribs) . ' </h3>';
                        echo '</div>';
                    }
                }
                $this->Menu->HtmlId = ''; ?>
                <?php
                if ($Session->IsValid()) {

                    if ($this->Menu) {
                        echo '<div id="MemberBar">';
                        $Name = $Session->User->Name;
                        $CountNotifications = $Session->User->CountNotifications;
                        if (is_numeric($CountNotifications) && $CountNotifications > 0)
                            $Name .= ' <span>' . $CountNotifications . '</span>';
                        $this->Menu->AddLink('SignOut', T('Sign Out'), $Authenticator->SignOutUrl(), FALSE, array('class' => 'NonTab SignOut'));
                        $this->Menu->AddLink('User', $Name, '/profile/{UserID}/{Username}', array('Garden.SignIn.Allow'), array('class' => 'UserNotifications'));
                        $this->Menu->RemoveGroup('Discussions'); //Get rid of the "All discussions" link
                        if (isset($this->Menu->Items['Conversations'])) { //check if using Conversations
                            $Conversations = $this->Menu->Items['Conversations']; //move this below
                            $this->Menu->RemoveGroup('Conversations'); //Get rid of the "All discussions" link
                        }



                        //$this->Menu->HtmlId = '';
                        $this->Menu->CssClass = "MainMenu";
                        echo UserPhoto($Session->User, array('LinkClass'=>'HeaderProfileImg','ImageClass'=>'ProfilePhotoSmall'));
                        echo $this->Menu->ToString();
                        $this->Menu->ClearGroups();
                        if (isset($Conversations))
                            $this->Menu->Items['Conversations'] = $Conversations; // add it back

                        $this->Menu->AddLink('Dashboard', T('Dashboard'), '/dashboard/settings', array('Garden.Settings.Manage'));
                        // $this->Menu->AddLink('Dashboard', T('Users'), '/user/browse', array('Garden.Users.Add', 'Garden.Users.Edit', 'Garden.Users.Delete'));
                        //$this->Menu->AddLink('Activity', T('All Activity'), '/activity');
                        $this->Menu->RemoveGroup('Discussions'); //Get rid of the "All discussions" link
                        //replace the array
                        $this->Menu->HtmlId = '';
                        //$this->Menu->ClearGroups();
                        $this->Menu->RemoveGroup('Discussions'); //Get rid of the "All discussions" link
                        echo $this->Menu->ToString();
                        $ViewLocation = Gdn::Controller()->FetchViewLocation('helper_functions', 'Discussions', 'vanilla');
                        include_once $ViewLocation;
                        WriteFilterTabs($this);
                        echo '</div>';
                    }
                }
                ?>

                <div id="Header">
                    <h1><a class="Title" href="<?php echo Url('/'); ?>"><span><?php echo Gdn_Theme::Logo(); ?></span></a></h1>
                </div>
                <div style="clear: both"></div>
            </div>
            <div id="Body">
                <div class="BodyInner">
                <?php
                unset($this->Assests['Panel']['NewDiscussionModule']);
                if (class_exists('TraditionalPlugin'))
                    TraditionalPlugin::CreateBreadcrumb($this, TRUE);
                ?>
                <?php ///@todo fix this hack of determing when to load the side panel  ?>
                <?php if ($this->ControllerName == 'profilecontroller'): ?>
                    <div id="Panel"><?php if (class_exists('TraditionalPlugin'))
                            TraditionalPlugin::profile($this); ?></div>
                    <div id="ProfileContainer">
                        <?php endif ?>
                    <div id="Content">
                        <div id="mainContainer">

<?php $this->RenderAsset('Content'); ?>
                        </div>
                        <?php
                        if (class_exists('TraditionalPlugin'))
                            TraditionalPlugin::GeneratePanel($this);
                        ?>

                <?php if ($this->ControllerName == 'profilecontroller'): ?>
                    </div>
            <?php endif ?>
                </div>
                    <div style="clear:both"></div>
                </div>
            </div>
<?php $this->FireEvent('AfterBody'); ?>

                </div>
            <?php $this->FireEvent('AfterFrame'); ?>
    <div id="Push"></div>
<?php $this->FireEvent('BeforeFoot'); //JJB added this   ?>
    <div id="Foot">
        <div id="FootInfo">
            <p>
                <a href="http://vanillaforums.org">Vanilla Forums</a>
            </p>
            <?php
            $this->RenderAsset('Foot');
            ?>
            <span style="font-style: italic; font-size: 7px">Layout by <a href="http://mcuhq.com">mcuhq </a></span>
    <!--            <a href='http://vanillaforums.org/'><img class="LogoVanilla" src="http://vanillaforums.com/blog/logos/vanilla_black2.png" width="80" height="40" /></a>-->
        </div>
    </div>
</body>
</html>
