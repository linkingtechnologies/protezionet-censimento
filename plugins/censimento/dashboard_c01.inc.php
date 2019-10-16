<?php
$camilaUI->insertTitle('Registrazione nucleo familiare', 'list-alt');

$camilaWT = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];

$wtFrom = 'OSPITI ATTESI';
$wtTo = 'OSPITI AREA ATTESA';
$ovCol1 = 'CODICE BADGE';
$template = 'ospiti area attesa_Bagde con QR Code.xml';

$wtFromId = $camilaWT->getWorktableSheetId($wtFrom);
$wtToId = $camilaWT->getWorktableSheetId($wtTo);

$ovColName1 = "";

$result2 = $camilaWT->getWorktableColumnsById($wtToId);
while (!$result2->EOF) {
	$b = $result2->fields;
	if (strtolower($ovCol1) == strtolower($b['name'])) {
		$ovColName1 = $b['col_name'];
	}
	$result2->MoveNext();
}


$lang = 'it';

$form = new phpform($_REQUEST['dashboard'],'index.php?dashboard=' . $_REQUEST['dashboard']);
$form->submitbutton = 'Cerca';
$form->drawrules = true;
$form->preservecontext = true;

new form_textbox($form, 'search', 'Nucleo familiare', false, 50, 200);
$form->fields['search']->autofocus = true;

if ($_REQUEST['cf'] != '')
	$form->fields['search']->value = $_REQUEST['cf'];
$form->fields['search']->set_css_class('form-control');

$inserted = false;
$ids = Array();
$overrides = Array();
$badge = $_REQUEST[$_REQUEST['dashboard'].'_badge'];
foreach ($_POST as $key => $value) {
	if (startsWith($key, $_REQUEST['dashboard'].'_children_')) {
		if (strpos($key, 'labels') === false && strpos($key, 'count') === false)
		{
			$v = $_POST[$key];
			if ($v != '') {
				$ids[] = $v;
				$overrides[$v]=Array($ovColName1=>$badge);
			}
		}
	}
}

if (count($ids) > 0 && $badge != '') {
	if (!$camilaWT->insertSuggestionRecords($wtFrom, $wtTo, implode($ids, ','),$overrides))
		camila_error_text("Si è verificato un errore nell'inserimento!");
	$inserted = true;
}

if (count($ids) > 0 && $badge == '') {
	camila_error_text("Inserire codice badge");
}

if (count($ids) == 0 && $badge != '') {
	camila_error_text("Selezionare almeno un componente del nucleo familiare");
}

if ($form->process())
{
	$nFam = strtoupper(trim($form->fields['search']->value));

	if ($nFam != '') {
		$sql = "Select Id,\${OSPITI ATTESI.COGNOME},\${OSPITI ATTESI.NOME},\${OSPITI ATTESI.INDIRIZZO ABITAZIONE},\${OSPITI ATTESI.CITTA' ABITAZIONE} FROM \${".$wtFrom."} WHERE UPPER(\${OSPITI ATTESI.IDENTIFICATIVO NUCLEO FAMILIARE})=".$camilaWT->db->qstr($nFam);
		$result = $camilaWT->startExecuteQuery($sql);
		if ($result->RecordCount() == 0) {
			$camilaUI->insertWarning('La ricerca non ha restituito alcun risultato!');
			$sql = "Select Id,\${OSPITI ATTESI.IDENTIFICATIVO NUCLEO FAMILIARE} FROM \${".$wtFrom."} WHERE UPPER(\${OSPITI ATTESI.CODICE FISCALE})=".$camilaWT->db->qstr($nFam);
			$result2 = $camilaWT->startExecuteQuery($sql);
			if ($result2->RecordCount() > 0) {
				$b = $result2->fields;
				$camilaUI->insertText('Il codice fiscale inserito appartiene al seguente nucleo familiare: ');
				$camilaUI->insertLink('?dashboard=c01&cf='.urlencode($b[1]), $b[1]);				
			}
		} else {
			$form->submitbutton = 'Registra';
			$gResLabels = array();
			$gResValues = array();
			while (!$result->EOF) {
				$a = $result->fields;
				$gResValues[] = $a[0];
				$gResLabels[] = $a[1].' '.$a[2] . ' | ' . $a[3].' | '.$a[4];
				$result->MoveNext();
			}

			new form_checklist($form, 'children', $nFam, $gResLabels, $gResValues, false, false);
			$form->fields['children']->cols = 1;
			//$form->draw();

			new form_textbox($form, 'badge', 'Codice Badge', true, 50, 200);
		}

		$text = new CHAW_text('');
		$_CAMILA['page']->add_text($text);
		if ($inserted) {
			camila_information_text('I dati sono stati inseriti correttamente!');
			
			$link = 'cf_worktable'.$wtToId.'.php?camila_xml2pdf='.urlencode($template).'&camila_w1f=_C_'.$ovColName1.'&camila_w1c=eq&camila_w1v='.urlencode($badge);
			$link .= '&filename='.urlencode($a[1]).'&camila_pagnum=-1&submit_button=Filtra+i+dati';
			$myLink = new CHAW_link('Stampa Badge QR Code', $link);
			$myLink->set_css_class('btn btn-md btn-default btn-info');
			$myLink->set_br(2);
			$_CAMILA['page']->add_link($myLink);

			$link = 'cf_worktable'.$wtToId.'.php?camila_w1f=_C_'.$ovColName1.'&camila_w1c=eq&camila_w1v='.urlencode($badge);
			$myLink = new CHAW_link('Visualizza dati inseriti', $link);
			$myLink->set_css_class('btn btn-md btn-default btn-info');
			$myLink->set_br(2);
			$_CAMILA['page']->add_link($myLink);
			
			
			$camilaUI->insertButton('?dashboard=c01', 'Registrazione nuovo nucleo familiare', 'list-alt');
			
		}
		else
			$form->draw();
	} else {
		camila_error_text('Inserire Identificativo Nucleo Familiare!');
		$form->draw();
	}
}
	else
		$form->draw();

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
?>