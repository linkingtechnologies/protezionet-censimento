<?php
$camilaWT  = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];


$g1Sheet = $camilaWT->getWorktableSheetId('OSPITI AREA ATTESA');
$g2Sheet = $camilaWT->getWorktableSheetId('OSPITI AREA INCONTRO');
 
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));	
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
$camilaUI->insertTitle('Area attesa', 'user');
$camilaUI->insertButton('?dashboard=c01', 'Registrazione nucleo familiare', 'list-alt');
$camilaUI->insertButton('cf_worktable'.$g1Sheet.'.php?camila_update=new', 'Registrazione ospite', 'plus');
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
$camilaUI->insertTitle('Area incontro', 'user');
$camilaUI->insertButton('?dashboard=c02', 'Registrazione tramite QR Code', 'list-alt');
$camilaUI->insertButton('cf_worktable'.$g2Sheet.'.php?camila_update=new', 'Registrazione ospite', 'plus');
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));

?>