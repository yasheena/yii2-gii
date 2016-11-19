<?php
namespace yasheena\gii\generators\crud;

use yii\base\Behavior;

class valuestore extends Behavior
{
	protected $vars = [];
	protected $touched = [];
	protected $names = [
		'col_' => ['visible', 'align', 'replace', 'replacename', 'width', 'search', 'label', 'order', 'name', 'format'],
		'dat_' => ['headerInfo', 'footerInfo', 'use_serialcolumn', 'use_checkboxcolumn', 'checkboxcolumn_field', 
					'enableI18N', 'enablePjax', 'messageCategory', 'lft_button', 'rgt_button'],
		'lft_' => ['label', 'url', 'icon', 'hint'],
		'rgt_' => ['label', 'url', 'icon', 'hint'],
	];
	protected $initDat = [
		'col_' => [],
		'dat_' => null,
		'lft_' => [],
		'rgt_' => [],
	];
	
	protected $ruleBoolean = ['use_serialcolumn', 'use_checkboxcolumn'];
	
	public function canSetProperty($name, $checkVars = true)
	{
		$pre = substr($name, 0, 4);
		return array_key_exists($pre, $this->names) && in_array(substr($name, 4), $this->names[$pre]);
	}
	
	public function canGetProperty($name, $checkVars = true)
	{
		$pre = substr($name, 0, 4);
		return array_key_exists($pre, $this->names) && in_array(substr($name, 4), $this->names[$pre]);
	}
	
    public function __get($name)
	{
		$this->force($name);
		return $this->vars[$name];
	}
	
	public function __set($name, $value)
	{
		$this->force($name);
		$this->vars[$name] = $value;
	}
	
	public function set($name, $index, $value)
	{
		$this->force($name);
		$this->vars[$name][$index] = $value;
	}
	
	public function is_set($name, $index)
	{
		$this->force($name);
		return array_key_exists($index, $this->vars[$name]) && ($this->vars[$name][$index] != null);
	}
	
	private function force($name)
	{
		if (!array_key_exists($name, $this->vars)) {
			$this->vars[$name] = $this->initDat[substr($name, 0, 4)];
		}
	}
	
	// Returns an array with indexes sorted on the values of the col_order array. 
	// Additional the values of col_order array are normalized. 
	public function sort()
	{
		asort($this->vars['col_order']);
		$n = 0;
		foreach ($this->vars['col_order'] as $index => $value) {
			$n += 10;
			$this->vars['col_order'][$index] = $n;
		}
		return array_keys($this->vars['col_order']);
	}
	
	public function getIndexOfName($name)
	{
		if (false === ($index = array_search($name, $this->vars['col_name']))) {
			$index = count($this->vars['col_name']);
			foreach ($this->names['col_'] as $nam) {
				$this->vars['col_' . $nam][$index] = null;
			}
			$this->vars['col_name'][$index] = $name;
			$this->vars['col_order'][$index] = 10000000 + $index;
		}
		$this->touched[$index] = true;
		return $index;
	}
	
	public function hasSearchField($name)
	{
		if (false === ($index = array_search($name, $this->vars['col_name']))) {
			return false;
		}
		return ($this->vars['col_visible'][$index] != 'hidden') && ($this->vars['col_search'][$index] != 'none'); 
	}
	
	public function getJSON()
	{
		$this->sort();
		return json_encode($this->vars);
	}
	
	public function setJSON($str)
	{
		$this->vars = json_decode($str, true);
	}
	
	public function removeUntouchedNames()
	{
		$touched = array_keys($this->touched);
		sort($touched);
		foreach($this->names['col_'] as $name) {
			$in = $this->vars['col_' . $name];
			$out = [];
			foreach ($touched as $index) {
				$out[] = $in[$index];
			}
			$this->vars['col_' . $name] = $out;
		}
		$this->touched = [];
	}
	
	public function rules()
	{
		$safe = [];
		$boolean = [];
		foreach ($this->names as $pre => $names) {
			foreach ($names as $name) {
				$nam = $pre . $name;
				if (in_array($nam, $this->ruleBoolean)) {
					$boolean[] = $nam;
				} else {
					$safe[] = $nam;
				}
			}
		}
		return [[$safe, 'safe'], [$boolean, 'boolean']];
	}
}

