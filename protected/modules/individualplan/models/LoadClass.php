<?php
/**
 * Created by PhpStorm.
 * User: george
 * Date: 13.06.2019
 * Time: 16:31
 */

class LoadClass
{
    /**
     * Функция формирует форму для выбора пунктов в подтверждение фактической нагрузки
     *
     * @param $data
     */
    public static function getFormConfirm($data){
        $result = '<div class="jumbotron" style="padding-left: 60px; padding-right: 60px; padding-top: 10px; padding-bottom: 10px"><center>';

        $sql = 'SELECT ipf.id, ipf.hours, ipf.correctHours FROM individualplan_planned_fixation ipf WHERE ipf.kindOfLoad = 1 AND ipf.fnpp = '.$data['fnpp'].' AND ipf.kind = '.$data['id'].' AND ipf.chair = '.$data['chair'].' AND ipf.year = '.$data['year'];
            $ipf = Yii::app()->db2->createCommand($sql)->queryRow();
        $sql = 'SELECT ipf.id, ipf.hours, ipf.correctHours FROM individualplan_planned_fixation ipf WHERE ipf.kindOfLoad = 2 AND ipf.fnpp = '.$data['fnpp'].' AND ipf.kind = '.$data['id'].' AND ipf.chair = '.$data['chair'].' AND ipf.year = '.$data['year'];
            $fipf = Yii::app()->db2->createCommand($sql)->queryRow();
        /*Верхнее меню модального окна*/
        $result .= '<h4>Плановый показатель <span class="label '.($ipf['hours']?($ipf['hours']!=$ipf['correctHours'])?'label-danger':'label-success':'label-default').'">';
        $result .= ($ipf['hours']?$ipf['hours']:0).'</span>';
        $result .= 'Подтвержденный плановый показатель <span class="label '.($ipf['correctHours']?'label-success':'label-default').'">'.($ipf['correctHours']?$ipf['correctHours']:0).'</span></h4>';
        $result .= '<h4>Фактический показатель <span class="label '.($fipf['hours']?($fipf['hours']!=$fipf['correctHours'])?'label-danger':'label-success':'label-default').'">';
        $result .= ($fipf['hours']?$fipf['hours']:0).'</span>';
        $result .= 'Подтвержденный фактический показатель <span class="label '.($fipf['correctHours']?'label-success':'label-default').'">'.($fipf['correctHours']?$fipf['correctHours']:0).'</span></h4>';
        $result .= '</center></div>';

        /*Проверяем сущетсвование подтверждающей строки*/
        $sql = 'SELECT ic.id, ic.comments, ic.answerComments, ic.text FROM individualplan_confirm ic WHERE ic.fnpp = '.$data['fnpp'].' AND ic.chair = '.$data['chair'].' AND ic.kindOfCatalog = '.$data['id'].' AND ic.year = '.$data['year'];
        $comments = Yii::app()->db2->createCommand($sql)->queryRow();
        if(!$comments){
            $insert = Yii::app()->db2->createCommand()->insert('individualplan_confirm', array(
                'fnpp' => $data['fnpp'],
                'ipf' => ($ipf['id']?$ipf['id']:null),
                'fipf' => ($fipf['id']?$fipf['id']:null),
                'chair' => $data['chair'],
                'kindOfCatalog' => $data['id'],
                'year' => $data['year'],
            ));
            $comments = Yii::app()->db2->createCommand($sql)->queryRow();
        }
        $result .= '<input type="hidden" id="main_id_confirm" value="'.$comments['id'].'">';
        $sql = 'SELECT * FROM individualplan_confirm_link icl WHERE icl.cconfirm = '.$comments['id'];
        $links = Yii::app()->db2->createCommand($sql)->queryAll();

        $sql = 'SELECT icr.*, ua.description, ut.sort, us.name status FROM individualplan_confirm_rating icr'
            .' LEFT JOIN uchet_activity ua ON ua.npp = icr.crating'
            .' LEFT JOIN uchet_types ut ON ut.npp = ua.tnpp'
            .' LEFT JOIN uchet_status us ON us.npp = ua.istatus'
            .' WHERE icr.cconfirm = '.$comments['id'];
        $ratings = Yii::app()->db2->createCommand($sql)->queryAll();

        $sql = 'SELECT icd.id, icd.name, icd.cconfirm, icd.size, icd.mime, icd.nameFile FROM individualplan_confirm_document icd'
            .' LEFT JOIN individualplan_confirm_files icf ON icf.id = icd.id'
            .' WHERE icd.cconfirm = '.$comments['id'];
        $files = Yii::app()->db2->createCommand($sql)->queryAll();



        if(true){
            $result .= '<div class="jumbotron" style="padding-left: 60px; padding-right: 60px; padding-top: 20px; padding-bottom: 20px">';

            /*блок текста*/
            $result .= '<div style="'.($comments['text'] != null?'':'display: none; ').'" class="modal_div_text">';
            $result .= '<center><label><u>Текстовое подтверждение</u></label></center>';
            $result .= '<textarea style="resize: vertical;" class="form-control change_text" rows="3" maxlength="255" placeholder="Текстовый коментарий к показателю">'.$comments['text'].'</textarea>';
            $result .= '<hr style="border-top-color: #d5d5d5;" />';
            $result .= '</div>';

            /*блок ссылок*/
            $result .= '<div style="'.($links?'':'display: none; ').'" class="modal_div_links">';
            $result .= '<center><label><u>Подтверждение ссылкой</u></label></center>';
            foreach ($links as $link){
                $result .= '<div class="input-group add-on">';
                $result .= '<input type="hidden" class="id_link" value="'.$link['id'].'">';
                $result .= '<input type="hidden" class="id_confirm" value="'.$comments['id'].'">';
                $result .= '<input type="text" class="form form-control text_link" value="'.$link['text'].'" style="padding: 3px 12px; margin: 3px 0px; height: auto;">';
                $result .= '<div class="input-group-btn" data-dismiss="alert">'
                    .'<button class="btn btn-default del_text_link" style="padding: 3px 12px; margin: 3px 0px;">x</button>'
                    .'</div>';
                $result .= '</div>';
            }
            $result .= '<div class="new_links_elem"></div>';
            $result .= '<hr style="border-top-color: #d5d5d5;" />';
            $result .= '</div>';

            /*блок рейтинга*/
            $result .= '<div style="'.($ratings?'':'display: none; ').'" class="modal_div_ratings">';
            $result .= '<center><label><u>Подтверждение показателем рейтинга</u></label></center>';
            $result .= '<table class="table table-condensed">';
            $result .= '<tr><th>Показатель</th><th>Название</th><th>Статус</th></tr>';
            foreach ($ratings as $rating){
                $result .= '<tr><td>'.$rating['sort'].'</td><td>'.$rating['description'].'</td><td>'.$rating['status'].'</td></tr>';
            }
            $result .= '</table>';
            $result .= '<hr style="border-top-color: #d5d5d5;" />';
            $result .= '</div>';

            /*блок файлов*/
            $result .= '<div style="'.($files?'':'display: none; ').'" class="modal_div_files">';
            $result .= '<center><label><u>Подтверждение файлом</u></label></center>';
            $result .= '<table class="table table-condensed">';
            $result .= '<tbody class="new_file_elem">';
            $result .= '<tr><th>Название</th><th>Размер</th><th>Файл</th></tr>';
            foreach ($files as $file){
                $result .= '<tr><td>'.$file['name'].'</td><td><center>'.$file['size'].'</center></td><td>'
                    .'<center>'
                    .'<input type="hidden" class="id_file" value="'.$file['id'].'">'
                    .CHtml::link('<button class="btn btn-info open_modal_file">'
                        .'<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Файл" class="glyphicon glyphicon-file">'
                        .'</button>', ['/individualplan/load/viewfile', 'id' => $file['id'], 'confirm' => $file['cconfirm']], ['target' => '_blank'])
                    .' '
                    .'<button class="btn btn-danger delete_modal_file">'
                    .'<span rel="tooltip" data-toggle="tooltip" data-placement="top" title="Удалить" class="glyphicon glyphicon-remove">'
                    .'</button>'
                    .'</center>'
                    .'</td></tr>';
            }
            $result .= '</tbody>';
            $result .= '</table>';
            $result .= '<hr style="border-top-color: #d5d5d5;" />';
            $result .= '</div>';



            $result .= '<div class="list-group" style="margin-bottom: 0px;">';
            $result .= '<button type="button" class="list-group-item add_modal_text" '.($comments['text'] != null?'style="background: lightgray" disabled="disabled"':'').'><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Добавить текст</button>';
            $result .= '<button type="button" class="list-group-item add_modal_links"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> Добавить ссылку (без лишнего текста)</button>';
            $result .= '<button type="button" class="list-group-item" style="background: lightgray" disabled="disabled"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> Подтвердить показателем рейтинга</button>';
            $result .= '<button type="button" class="list-group-item add_modal_files"><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Загрузить документ</button>';
            $result .= '</div>';
            $result .= '</div>';
        }

        /*Коментарии к показателям*/
        $result .= '<div class="jumbotron" style="padding-left: 60px; padding-right: 60px; padding-top: 20px; padding-bottom: 20px">';
        $result .= '<label>Информация об отклонение часов:</label>';
        $result .= '<textarea style="resize: vertical;" class="form-control text_comments" rows="3" maxlength="255" placeholder="Оставить информацию об отклонение часов от плановых...">'.$comments['comments'].'</textarea>';
        $result .= '<br /><input type="hidden" class="id" value="'.$data['id'].'" >';
        $result .= '</div>';

        $result .= '<div class="jumbotron" style="padding-left: 60px; padding-right: 60px; padding-top: 20px; padding-bottom: 20px">';
        $result .= '<label>Комментарий заведующего кафедрой:</label>';
        $result .= '<textarea disabled="disabled" style="resize: vertical; background-color: white" class="form-control" rows="3" maxlength="255">'.$comments['answerComments'].'</textarea>';
        $result .= '</div>';
        /*удалить после отладки*/
        if(false) {
            $result .= '<input type="text" value="' . $data['id'] . '"><br />';
            //$result .= '<input type="text" value="' . $data['confirm'] . '"><br />';
            $result .= '<input type="text" value="' . $data['kind'] . '"><br />';
            $result .= '<input type="text" value="' . $data['fnpp'] . '"><br />';
            $result .= '<input type="text" value="' . $data['year'] . '"><br />';
            $result .= '<input type="text" value="' . $data['chair'] . '"><br />';
            $result .= '<input type="text" value="' . $data['idCatalog'] . '">';
        }



        return $result;
    }
}