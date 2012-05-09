<?php if (!defined('APPLICATION')) exit();

/**
 * Took out the "Comment by: $username"....if we are looking at a profile we should know
 * that all of the listed comments are from that user
 *
 * Added time/date to permalink
 */

foreach ($this->CommentData->Result() as $Comment) {
	$Permalink = '/discussion/comment/'.$Comment->CommentID.'/#Comment_'.$Comment->CommentID;
	$User = UserBuilder($Comment, 'Insert');
	$this->EventArguments['User'] = $User;
?>
<li class="Item">
	<?php $this->FireEvent('BeforeItemContent'); ?>
	<div class="ItemContent">
            <div class="Meta">
			<span><?php echo Anchor(T(Gdn_Format::Date($Comment->DateInserted)), $Permalink, array('class' => 'DiscussionDate')); ?></span>
		</div>
		<?php echo Anchor(Gdn_Format::Text($Comment->DiscussionName), $Permalink, 'Title');
                ?>
		<div class="Excerpt"><?php
			echo Anchor(SliceString(Gdn_Format::Text(Gdn_Format::To($Comment->Body, $Comment->Format), FALSE), 250), $Permalink);
		?></div>
	</div>
</li>
<?php
}