<?php /* Smarty version 2.6.0, created on 2010-03-02 18:26:02
         compiled from tutorial_tree.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strip_tags', 'tutorial_tree.tpl', 1, false),)), $this); ?>
<div><img class="tree-icon" src="<?php echo $this->_tpl_vars['subdir']; ?>
media/images/tutorial.png" alt="Tutorial"><a href="<?php echo $this->_tpl_vars['main']['link']; ?>
" target="right"><?php echo ((is_array($_tmp=$this->_tpl_vars['main']['title'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
</a></div>
<?php if ($this->_tpl_vars['haskids']): ?>
<div style="margin-left: 19px">
	<?php echo $this->_tpl_vars['kids']; ?>

</div>
<?php endif; ?>