<?php

class PaavTreeView extends CWidget
{
    public $dataProvider;
    public $hiddenData = array();

    protected $items = array();
    protected $clickedItem;

    public function init()
    {
        if (!isset($this->dataProvider))
            throw new CException(
                "You must provide 'dataProvider' property initial value.");

       foreach ($this->dataProvider->getData() as $data) 
           $this->items[] = new PaavTreeViewItem($this, $data);

        $assetsPath = dirname(__FILE__) . '/assets';

        $cssFiles = [
            'main.css',
            'fontello.css',
        ];

        $app = Yii::app();

        $am = $app->assetManager;
        $cs = $app->clientScript;

        $assetsUrl = $am->publish($assetsPath, false, -1, true);

        foreach ($cssFiles as $cssFile)
            $cs->registerCssFile($assetsUrl . '/css/' . $cssFile);
    }

    public function run()
    {
        $this->renderTree();

        if (!empty($this->hiddenData) && isset($this->clickedItem))
            $this->renderHidden();
    }

    public function renderTree()
    {
        $items = $this->getRootItems();
        
        echo '<ul class="paavTreeView">';

        $this->renderItems($items);

        echo '</ul>';
    }
    
    public function renderItems($items)
    {
        foreach ($items as $item) {
            $item->render();

          if ($item->isClicked() || $this->hasClicked($item->id)) {
              echo '<ul class="paavTreeView paavTreeView-subItems">';
              $this->renderItems($this->getChildItems($item->id));
              echo '</ul>';
          }
        }
    }

    protected function renderHidden()
    {
        list($model, $attr) = $this->hiddenData;

        echo CHtml::activeHiddenField($model, $attr);
    }
    
    
    public function getItems()
    {
       return $this->items; 
    }
    
    public function setClicked(PaavTreeViewItem $item)
    {
       $this->clickedItem = $item; 

       return true;
    }
    
    protected function getRootItems()
    {
        $rootItems = array();

        foreach ($this->items as $item)
            if ($item->getParentId() === null)
                $rootItems[] = $item;

        return $rootItems;
    }
    
    protected function getChildItems($id)
    {
        $childItems = array();

        foreach ($this->items as $item)
            if ($item->getParentId() === $id)
                $childItems[] = $item;

        return $childItems;
    }

    protected function findById($id)
    {
        foreach ($this->items as $item)
            if ($item->getId() === $id)
                return $item;    

        return null;
    }

    // Work only for 2 level hierarchy
    protected function hasClicked($id)
    {
        foreach ($this->items as $item)
            if ($item->isClicked() && $item->getParentId() === $id)
                return true;    

        return false;
    }
}

class PaavTreeViewItem extends CComponent
{
    protected $isClicked = false;
    protected $tree;
    protected $data;
    protected $isLeaf;
    protected $isRoot;

    public function __construct($tree, $data)
    {
        $this->tree = $tree;
        $this->data = $data;

        if(isset($_GET['ptvi_id']) && $_GET['ptvi_id'] === $this->getId()) {
            $this->tree->setClicked($this);
            $this->isClicked = true;
        }
    }

    public function getId()
    {
       return $this->data->id; 
    }
    
    public function getParentId()
    {
       return $this->data->parent_id; 
    }
    
    public function getName()
    {
       return $this->data->name; 
    }

    public function isClicked()
    {
       return $this->isClicked; 
    }
    
    public function render()
    {
        $classes[] = 'paavTreeViewItem';

        if ($this->isRoot() || !$this->isLeaf()) {

            if ($this->isClicked)
                $classes[] = 'paavTreeViewItem-expand';

            else
                $classes[] = 'paavTreeViewItem-collapse';

        } elseif ($this->isLeaf()) {
            $classes[] = 'paavTreeViewItem-leaf';

            if ($this->isClicked())
                $classes[] = 'paavTreeViewItem-mark';
        }

        $output = '<li class="' . implode(' ', $classes) . '">';
        $output .= CHtml::link($this->getName(), '?ptvi_id=' . $this->id);
        $output .= '</li>';

        echo $output;
    }
    
    protected function isLeaf()
    {
        if (isset($this->isLeaf))
            return $this->isLeaf;

        $is = true;

        $items = $this->tree->getItems();

        foreach ($items as $item)
            if ($item->getParentId() === $this->getId())
                $is = false;

        return $this->isLeaf = $is;
    }

    protected function isRoot()
    {
        if (isset($this->isRoot))
            return $this->isRoot;

        return $this->isRoot = $this->data->parent_id === null; 
    }
}
