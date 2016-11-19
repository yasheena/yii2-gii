<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

use yii\helpers\Html;

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'viewPath');
echo Html::a('Load', ['load'], ['class' => 'btn btn-primary generator-loadview']);
echo $form->field($generator, 'baseControllerClass');
if ($generator->generated) {
	yasheena\gii\YasheenaGiiAsset::register($this);
	echo $form->field($generator, 'enableI18N')->checkbox();
	echo $form->field($generator, 'enablePjax')->checkbox();
	echo $form->field($generator, 'messageCategory');
	echo '<h2>Configuration of the index page:</h2><br />';
	echo $form->field($generator, 'dat_headerInfo');
	echo $form->field($generator, 'dat_footerInfo');
	echo $form->field($generator, 'dat_use_serialcolumn')->checkbox();
	echo "<br />\n";
	echo $form->field($generator, 'dat_use_checkboxcolumn')->checkbox();
	echo $form->field($generator, 'dat_lft_button')->radioList($generator->getListButtonType());
	echo "<div class=\"form-group field-generator-lft_table\"><table class=\"coltab\">\n<tr>"
		. $generator->colLabel('Text', '<b>Left buttons</b>: Text on button<br><b>Left dropdown:</b> 1st line: text for dropdown button<br>2nd and following lines: text for dropdown list<br><br>(Click on "Preview" to get additional rows)<br>(Leave "Text" empty, set "Icon" to "---" and click on "Preview" to remove thise row)')
		. $generator->colLabel('Url', 'Left buttons / dropdown:<br>&lt;name&gt; &nbsp; =&gt; &nbsp; Action name of actual controller<br>/&lt;path&gt; &nbsp; =&gt; &nbsp; Path within this application<br>http://&lt;url&gt; &nbsp; =&gt; &nbsp; Path to external url')
		. $generator->colLabel('Icon', '<b>Left buttons</b>: Icon to use for the button<br><b>Left dropdown</b>: 1st line: icon to use for the dropdown button<br>2nd and following lines: icons to use for or dropdown list entries')
		. "<td></td>"
		. $generator->colLabel('Hint', 'Left buttons / dropdown: Hint text for this entry')
		. "</tr>\n";
	foreach ($generator->lft_label as $index => $label) {
		echo '<tr id="generator-lft-line-' . $index . '"><td class="btn_label">' . $form->field($generator, "lft_label[$index]", ['template' => "{input}\n{hint}\n{error}"])
			. '</td><td class="btn_url">' . (($index == 0) ? $form->field($generator, "lft_url[$index]")->hiddenInput()->label(false) : $form->field($generator, "lft_url[$index]", ['template' => "{input}\n{hint}\n{error}"]))
			. '</td><td class="btn_icon">' . $form->field($generator, "lft_icon[$index]", ['template' => "{input}\n{hint}\n{error}"])->dropDownList($generator->getListIcons(), ['class' => 'generator-button-dropdown', 'id' => 'generator-lft-button-' . $index, 'options' => $generator->getGlyphiconOptions()])
			. '</td><td class="btn_icon_pic"><span id="generator-lft-button-' . $index . '-icon"></span>'
			. '</td><td class="btn_hint">' . $form->field($generator, "lft_hint[$index]", ['template' => "{input}\n{hint}\n{error}"])
			. "</td></tr>\n";
	}
	echo "</table></div>\n";
	echo $form->field($generator, 'dat_checkboxcolumn_field')->dropDownList($generator->getCheckboxcolumnField(), ['class' => 'generator-button-dropdown', 'id' => 'generator-checkbox-field']);
	echo "<br />\n";
	echo $form->field($generator, 'dat_rgt_button')->radioList($generator->getListButtonType());
	echo "<div class=\"form-group field-generator-rgt_table\"><table class=\"coltab\">\n<tr>"
		. $generator->colLabel('Text', '<b>Right buttons</b>: Text on button<br><b>Right dropdown:</b> 1st line: text for dropdown button<br>2nd and following lines: text for dropdown list<br><br>(Click on "Preview" to get additional rows)<br>(Leave "Text" empty, set "Icon" to "---" and click on "Preview" to remove thise row)')
		. $generator->colLabel('Url', 'Right buttons / dropdown:<br>&lt;name&gt; &nbsp; =&gt; &nbsp; Action name of actual controller<br>/&lt;path&gt; &nbsp; =&gt; &nbsp; Path within this application<br>http://&lt;url&gt; &nbsp; =&gt; &nbsp; Path to external url')
		. $generator->colLabel('Icon', '<b>Right buttons</b>: Icon to use for the button<br><b>Right dropdown</b>: 1st line: icon to use for the dropdown button<br>2nd and following lines: icons to use for or dropdown list entries')
		. "<td></td>"
		. $generator->colLabel('Hint', 'Right buttons / dropdown: Hint text for this entry')
		. "</tr>\n";
	foreach ($generator->rgt_label as $index => $label) {
		echo '<tr id="generator-rgt-line-' . $index . '"><td class="btn_label">' . $form->field($generator, "rgt_label[$index]", ['template' => "{input}\n{hint}\n{error}"])
			. '</td><td class="btn_url">' . (($index == 0) ? $form->field($generator, "rgt_url[$index]")->hiddenInput()->label(false) : $form->field($generator, "rgt_url[$index]", ['template' => "{input}\n{hint}\n{error}"]))
			. '</td><td class="btn_icon">' . $form->field($generator, "rgt_icon[$index]", ['template' => "{input}\n{hint}\n{error}"])->dropDownList($generator->getListIcons(), ['class' => 'generator-button-dropdown', 'id' => 'generator-rgt-button-' . $index, 'options' => $generator->getGlyphiconOptions()])
			. '</td><td class="btn_icon_pic"><span id="generator-rgt-button-' . $index . '-icon"></span>'
			. '</td><td class="btn_hint">' . $form->field($generator, "rgt_hint[$index]", ['template' => "{input}\n{hint}\n{error}"])
			. "</td></tr>\n";
	}
	echo "</table></div><br />\n";
	echo "<h3>Column configuration:</h3><br /><div class=\"form-group\"><table class=\"coltab\">\n<tr>"
		. $generator->colLabel('Order', 'Modify the numbers to get a new order of the fields in the resulting view.')
		. $generator->colLabel('Database field', 'List of all fields in the given model.')
		. $generator->colLabel('Visibility', '<b>visible</b>: Column is visible on default<br><b>invisible</b>: Column is invisible on default<br><b>hidden</b>: Column will never be visible')
		. $generator->colLabel('Alignment', 'Set the alignment for the column content.')
		. $generator->colLabel('Width', 'Optional: Use <b>px</b> or <b>%</b> to define the default column width, i.e. "120px" or "15%".')
		. $generator->colLabel('Format', '<b>text</b>: Default view as text (\n and \r are ignored)<br><b>ntext</b>: Show multiline values as multiple lines<br><b>ntext1line</b>: Multiline values => view first line only<br>'
				. '<b>ntextshort</b>: Like ntext1line but shortened to 30 chars<br><b>boolean</b>: View "yes" and "no" instead of number<br>'
				. '<b>integer</b>: Show as integer<br><b>decimal</b>:Show as decimal (default: 2 decimal places)<br><b>currency</b>: Format with actual currency settings<br><b>percent</b>: Show as percent (1 => 100 %)<br>'
				. '<b>timestamp</b>: Formats value as UNIX timestamp (float)<br><b>datetime</b>: Show integer as date and time<br><b>date</b>: Show date part of integer value only<br><b>time</b>: Show time part of integer value only<br>'
				. '<b>email</b>: Format as direct clickable email address<br><b>url</b>: Format as direct clickable link<br><b>html</b>: Use the HtmlPurifier (to avoid XSS attacks)<br>'
				. '<b>image</b>: Show image with the value as url<br><b>spellout</b>: Show the number as written text<br><b>size</b>: Formats the value in a human readable byte format')
		. $generator->colLabel('Searchfield', '<b>text</b>: Default search field (enter text)<br><b>list</b>: Select one entry outof a list with all column values<br><b>none</b>: Remove search area for this column')
		. $generator->colLabel('Replace column by', 'Replace this column with the column of the connected table (foreign key).')
		. $generator->colLabel('Label', 'Optional: Replaces the original label text.')
		. "</tr>\n";
	foreach ($generator->getBehavior('valuestore')->sort() as $index) {
		$replList = $generator->col_replacename[$index];
		echo '<tr><td class="col_order">' . $form->field($generator, "col_order[$index]", ['template' => "{input}\n{hint}\n{error}"])
			. '</td><td class="col_name">' . $form->field($generator, "col_name[$index]", ['template' => "{input}\n{hint}\n{error}"])->textInput(['readonly' => true])
			. '</td><td class="col_visible">' . $form->field($generator, "col_visible[$index]", ['template' => "{input}\n{hint}\n{error}"])->dropDownList($generator->getListVisible())
			. '</td><td class="col_align">' . $form->field($generator, "col_align[$index]", ['template' => "{input}\n{hint}\n{error}"])->dropDownList($generator->getListAlign()) 
			. '</td><td class="col_width">' . $form->field($generator, "col_width[$index]", ['template' => "{input}\n{hint}\n{error}"])
			. '</td><td class="col_format">' . $form->field($generator, "col_format[$index]", ['template' => "{input}\n{hint}\n{error}"])->dropDownList($generator->getListFormat()) 
			. '</td><td class="col_search">' . $form->field($generator, "col_search[$index]", ['template' => "{input}\n{hint}\n{error}"])->dropDownList($generator->getListSearch()) 
			. '</td><td class="col_replace">' . ($replList == null 
					? $form->field($generator, "col_replace[$index]", ['template' => "{input}\n{hint}\n{error}"])->hiddenInput() 
					: $form->field($generator, "col_replace[$index]", ['template' => "{input}\n{hint}\n{error}"])->dropDownList($replList))
			. '</td><td class="col_label">' . $form->field($generator, "col_label[$index]", ['template' => "{input}\n{hint}\n{error}"])
			. "</td></tr>\n";  
	}
	echo "</table></div><br />\n";
}
