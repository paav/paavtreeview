<?php
// vim:ft=htmlphp

/* @var $items array of PaavTreeViewItem objects */
?>

<ul class="paavTreeView">
  <?php foreach ($items as $item): ?>

  <li class="paavTreeViewItem paavTreeViewItem-expand">
    <?php $item->render(); ?>
  </li>

  <?php if ($item->isOpened): ?>
  <li class="">
    <?php $this->render('main', array('items'=>$this->getChildItems($item->id))); ?>
  </li>
  <?php endif; ?>

  <?php endforeach; ?>
</ul>
