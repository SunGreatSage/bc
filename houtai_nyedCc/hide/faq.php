<?php
include('../data/comm.inc.php');
include('../data/myadminvar.php');
include('../func/func.php');
include('../func/adminfunc.php');
include('../global/page.class.php');
include('../include.php');
include('./checklogin.php');
switch ($_REQUEST['xtype']) {
    case "show":
        $msql->query("select * from `$tb_faq` order by time desc");
        $i = 0;
        while ($msql->next_record()) {
            $news[$i]['id']      = $msql->f('id');
            $news[$i]['title']   = $msql->f('title');
            $news[$i]['content'] = $msql->f('content');
            $news[$i]['time']    = date("m月d日 H:i", $msql->f('time'));
            $i++;
        }
        include("../fckeditor/fckeditor.php");
        $oFCKeditor           = new FCKeditor('content');
        $oFCKeditor->BasePath = "../fckeditor/";
        $oFCKeditor->Value    = $value;
        $oFCKeditor->Height   = 300;
        $content              = $oFCKeditor->CreateHtml();
        $tpl->assign('content', $content);
        $tpl->assign('news', $news);
        $tpl->display("faq.html");
        break;
    case "addnews":
        $content = $_POST['content'];
        $title   = $_POST['ftitle'];
        $time    = time();
        $sql     = "insert into `$tb_faq` set content='$content',title='$title',time=NOW()";
        if ($msql->query($sql)) {
            echo openurl("faq.php?xtype=show");
        }
        break;
    case "newsdel":
        $id = $_POST['id'];
        if ($msql->query("delete from `$tb_faq` where instr('$id',concat('|',id,'|'))")) {
            echo 1;
        }
        break;
}
?>