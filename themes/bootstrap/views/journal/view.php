<?php
/* @var $this JournalController */
/* @var $model Journal */


$this->menu=$menu;
$this->pageTitle = 'Электронный журнал посещаемости';
?>

<h1>Журнал посещаемости</h1>
<?php
echo CHtml::beginForm('','post',array("id" => "markStatSave", 'align' => "center", 'data-form-confirm' => "modal__confirm"));
?>
<button type="submit" name="down" class="btn btn-info btn-sm" value="Previous"><<Предыдущая неделя</button>
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker',array(
	'name'=>'publishDate',
	'value' => $date,
	'language'=>'ru',
	'options'=>array(
		'dateFormat' => "yy-mm-dd",
		'minDate' => '2016-09-01',
		'showAnim'=>'slideDown',
		//'showAnim'=>'bounce',
		'firstDay' => 1,
	),
	'htmlOptions'=>array(
		'style'=>'height:20px;'
	),
));
echo" ";
?>
<button type="submit" name="down" class="btn btn-primary btn-sm" value="Send">Перейти на дату</button>
<button type="submit" name="down" class="btn btn-info btn-sm" value="Next">Следующая неделя>></button>
<?php
echo CHtml::endForm();
?>
<?= $this->renderPartial('_tabsview', ['dates' => $dates, 'day' => $day, 'activedates' => $activedates]); ?>
<?php
	if ($discipline) {
		$numbers = 1;
		foreach ($discipline as $dis) {
			$numbers++;
		}
		$width = 100.0 / $numbers;
		?>
		<div style="position:relative">
			<div style="overflow-x:scroll; overflow-y:visible; width:100%; margin-left:200px; width:625px; ">
				<table class=" table table-striped _table-ulist static table-hover"
					   style="table-layout:fixed; width: 100%;">
					<thead>
					<tr>
						<th style="position:absolute; left:0; width:200px; vertical-align: bottom; height:100%;">
							ФИО
						</th>
						<?php
						foreach ($discipline as $dis) {
							if ($numbers <= 2) {
								echo '<th width="625 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
							} else {
								if ($numbers <= 3) {
									echo '<th width="312 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
								} else {
									if ($numbers <= 4) {
										echo '<th width="208 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
									} else {
										echo '<th width="200 px" colspan="2" style="border-left: 1px solid darkgray ">' . $dis['discipline'] . '<br/>' . '(' . $dis['Kind'];
									}
								}
							}
							$i = 0;
							while ($i < 10) {
								$i++;
								if (strstr($dis['studGroupName'], '/' . $i)) {
									echo '-' . $i;
									break;
								}
							}
							echo ')' . '(' . $dis['teacherFio'] . ')' . '<br/>' . $dis['dat'] . '<br/>' . $dis['time'];
							echo '</th>';
						}
						?>
					</tr>
					<tr>
						<th></th>
						<?php
						foreach ($discipline as $dis) {
							echo '<th style="border-left: 1px solid darkgray">Ст.</th>';
							echo '<th>ППС</th>';
						}
						?>
					</tr>
					</thead>
					<tbody>
					<?php
					if ($steward) {
						foreach ($list as $li) {
							echo '<tr><th style="position:absolute; left:0; width:200px;">' . $li['fio'] . '</th>';
							$i = 0;
							foreach ($discipline as $dis) {
								if (in_array($this->mark($li['fnpp'], $dis['id']), [1])) {
									echo '<td class="success" style="border-right: 1px solid darkgray; padding:10px;">Явка</td>';
								} else {
									if (in_array($this->mark($li['fnpp'], $dis['id']), [2])) {
										echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Н/я ув.</td>';
									} else {
										if (in_array($this->mark($li['fnpp'], $dis['id']), [5])) {
											echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Не сост.</td>';
										} else {
											if (in_array($this->mark($li['fnpp'], $dis['id']), [4])) {
												echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Др/подгр.</td>';
											} else {
                                                if (in_array($this->mark($li['fnpp'], $dis['id']), [6])) {
                                                    echo '<td class="success" style="border-right: 1px solid darkgray; padding:10px;">Дист.</td>';
                                                } else {
                                                    echo '<td class="danger" style="border-right: 1px solid darkgray; padding:10px;">Н/я н/у</td>';
                                                }
											}
										}
									}
								}
								if (in_array($this->markteach($li['fnpp'], $dis['id']), [1])) {
									echo '<td class="success" style="border-right: 1px solid darkgray; padding:10px;" >Явка</td>';
								} else {
									if (in_array($this->markteach($li['fnpp'], $dis['id']), [2])) {
										echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Н/я ув.</td>';
									} else {
										if (in_array($this->markteach($li['fnpp'], $dis['id']), [5])) {
											echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Не сост.</td>';
										} else {
											if (in_array($this->markteach($li['fnpp'], $dis['id']), [4])) {
												echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Др/подгр.</td>';
											} else {
                                                if (in_array($this->markteach($li['fnpp'], $dis['id']), [6])) {
                                                    echo '<td class="success" style="border-right: 1px solid darkgray; padding:10px;" >Дист.</td>';
                                                } else {
                                                    echo '<td class="danger" style="border-right: 1px solid darkgray; padding:10px;">Н/я н/у</td>';
                                                }
											}
										}
									}
								}
							}
							echo '</tr>';
						}
					} else {
						echo '<tr><th style="position:absolute; left:0; width:200px;">' . $name . '</th>';
						foreach ($discipline as $dis) {
							if (in_array($this->mark($fnpp, $dis['id']), [1])) {
								echo '<td class="success" style="border-right: 1px solid darkgray; padding:10px;">Явка</td>';
							} else {
								if (in_array($this->mark($fnpp, $dis['id']), [2])) {
									echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Н/я ув.</td>';
								} else {
									if (in_array($this->mark($fnpp, $dis['id']), [5])) {
										echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Не сост.</td>';
									} else {
										if (in_array($this->mark($fnpp, $dis['id']), [4])) {
											echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Др/подгр.</td>';
										} else {
                                            if (in_array($this->mark($fnpp, $dis['id']), [6])) {
                                                echo '<td class="success" style="border-right: 1px solid darkgray; padding:10px;">Дист.</td>';
                                            } else {
                                                echo '<td class="danger" style="border-right: 1px solid darkgray; padding:10px;">Н/я н/у</td>';
                                            }
										}
									}
								}
							}
							if (in_array($this->markteach($fnpp, $dis['id']), [1])) {
								echo '<td class="success" style="border-right: 1px solid darkgray; padding:10px;">Явка</td>';
							} else {
								if (in_array($this->markteach($fnpp, $dis['id']), [2])) {
									echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Н/я ув.</td>';
								} else {
									if (in_array($this->markteach($fnpp, $dis['id']), [5])) {
										echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Не сост.</td>';
									} else {
										if (in_array($this->markteach($fnpp, $dis['id']), [4])) {
											echo '<td class="info" style="border-right: 1px solid darkgray; padding:10px;">Др/подгр.</td>';
										} else {
                                            if (in_array($this->markteach($fnpp, $dis['id']), [6])) {
                                                echo '<td class="success" style="border-right: 1px solid darkgray; padding:10px;">Дист.</td>';
                                            } else {
                                                echo '<td class="danger" style="border-right: 1px solid darkgray; padding:10px;">Н/я н/у</td>';
                                            }
										}
									}
								}
							}
						}
						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	} else {
		if ($day) {
			echo '<center>Статистика отсутствует</center>';
		} else {
			echo '<center>Выходной</center>';
		}
	}
	?>
	<br/>
	<table class=" table table-striped _table-ulist static table-hover" border="1">
		<tr>
			<th style="border-top: 1px solid black">Н/я н/у</th>
			<td style="border-top: 1px solid black">Студент отсутствовал, по не уважительной причине</td>
		</tr>
        <tr>
            <th>Явка</th>
            <td>Студент присутствал на паре очно</td>
        </tr>
        <tr>
            <th>Дист.</th>
            <td>Студент присутствал на паре дистанционно</td>
        </tr>
		<tr>
			<th>Н/я ув.</th>
			<td>Студент отсутствовал, по уважительной причине</td>
		</tr>
		<tr>
			<th>Не сост.</th>
			<td>Пара не состоялась</td>
		</tr>
		<tr>
			<th>Др/подгр.</th>
			<td>Студент находится в другой подгруппе</td>
		</tr>
		<tr>
			<th style="border-top: 1px solid black">Ст.</th>
			<td style="border-top: 1px solid black">Отметка о посещаемости старостой</td>
		</tr>
		<tr>
			<th>ППС</th>
			<td>Отметка о посещаемости преподавателем</td>
		</tr>
	</table>