<?php
$camilaWT  = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];

$g1Sheet = $camilaWT->getWorktableSheetId('OSPITI AREA ATTESA');
$g2Sheet = $camilaWT->getWorktableSheetId('OSPITI AREA INCONTRO');
$g3Sheet = $camilaWT->getWorktableSheetId('BADGE AREA INCONTRO');
$g4Sheet = $camilaWT->getWorktableSheetId('ANIMALI DI AFFEZIONE');
 
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));	
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-6">'));
$camilaUI->insertTitle('Area attesa', 'user');
$camilaUI->insertButton('?dashboard=c01', 'Registrazione nucleo familiare', 'list-alt');
$camilaUI->insertButton('?dashboard=c03', 'Ricerca nucleo familiare', 'search');
$camilaUI->insertButton('cf_worktable'.$g1Sheet.'.php?camila_update=new', 'Registrazione ospite', 'plus');
$camilaUI->insertButton('cf_worktable'.$g4Sheet.'.php?camila_update=new', 'Registrazione animale di affezione', 'plus');
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-6">'));
$camilaUI->insertTitle('Area incontro', 'user');
$camilaUI->insertButton('?dashboard=c02', 'Check-in tramite QR Code', 'qrcode');
$camilaUI->insertButton('cf_worktable'.$g3Sheet.'.php?camila_update=new', 'Check-in tramite codice a barre', 'barcode');
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));

?>