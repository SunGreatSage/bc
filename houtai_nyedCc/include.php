<?php
/*
 * Created on 2007-5-14
 * Programmer : Alan , Msn - haowubai@hotmail.com
 * PHP100.com Develop a project PHP - MySQL - Apache
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include_once("../class/Smarty.class.php");
 //******************
 
    $tpl = new smarty();
    $tpl->template_dir = '../templates/'.$skin.'/'.$smartytpl;
    $tpl->compile_dir  = './temp_dc/';
    $tpl->config_dir   = './config/';
    $tpl->cache_dir    = './cache/';
    $tpl->caching      = false;
    $tpl->left_delimiter = "{+";
    $tpl->right_delimiter = "+}";
 //******************

?>