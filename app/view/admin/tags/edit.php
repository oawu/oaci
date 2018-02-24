<div class='back'>
  <a href="<?php echo RestfulUrl::index ();?>" class='icon-36'>回上一頁</a>
</div>

<?php echo $form->appendFormRows (
  Restful\Text::need ('名稱', 'name')->setAutofocus (true)->setMaxLength (255)
);?>