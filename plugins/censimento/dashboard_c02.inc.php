<?php
$camilaUI->insertTitle('Registrazione area incontro', 'list-alt');

$camilaWT = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];

$wtFrom = 'OSPITI ATTESI';
$wtTo2 = 'OSPITI AREA INCONTRO';

$wtFromId = $camilaWT->getWorktableSheetId($wtFrom);
$wtTo2Id = $camilaWT->getWorktableSheetId($wtTo2);

$ovCol1 = "COGNOME";
$ovColName1 = "";
$ovCol2 = "NOME";
$ovColName2 = "";
$ovCol3 = "IDENTIFICATIVO NUCLEO FAMILIARE";
$ovColName3 = "";
$ovCol4 = "CODICE BADGE";
$ovColName4 = "";

$result2 = $camilaWT->getWorktableColumnsById($wtTo2Id);
while (!$result2->EOF) {
	$b = $result2->fields;
	if (strtolower($ovCol1) == strtolower($b['name'])) {
		$ovColName1 = $b['col_name'];
	}
	if (strtolower($ovCol2) == strtolower($b['name'])) {
		$ovColName2 = $b['col_name'];
	}
	if (strtolower($ovCol3) == strtolower($b['name'])) {
		$ovColName3 = $b['col_name'];
	}
	if (strtolower($ovCol4) == strtolower($b['name'])) {
		$ovColName4 = $b['col_name'];
	}
	$result2->MoveNext();
}

$lang = 'it';
$camilaTemplate = new CamilaTemplate($lang);
$params = $camilaTemplate->getParameters();

$form = new phpform($_REQUEST['dashboard'],'index.php?dashboard=' . $_REQUEST['dashboard']);
$form->submitbutton = 'Registra';
$form->drawrules = true;
$form->preservecontext = true;

new form_textarea($form, 'search', 'QR Code', true, 5, 80);
$form->fields['search']->set_css_class('form-control');
$form->fields['search']->autofocus = true;

$inserted = false;
$ids = Array();

if ($form->process())
{
	$qr = base64_decode($form->fields['search']->value);
	$k0 = '';
	$k1 = '';

	if ($qr != '') {
		$del1 = '%';
		$del2 = '|';

		$suggIds = Array();
		$overrides = Array();
		$arr = explode($del1, $qr);
		if (count($arr)>1) {
			camila_information_text('I seguenti dati sono stati registrati.');
			foreach($arr as $k => $v) {
				if ($k == 0) {
					$k0 = $v;
				}
				if ($k == 1) {
					$k1 = $v;
				}
				if ($k == 2) {
					camila_information_text('Area Attesa: ' . $k1 .' | Numero persone: ' . $v . ' | Codice badge: ' . $k0);
				}
				if ($k > 2) {
					$row = explode($del2, $v);
					$cf = $row[0];
					if ($cf != '') {
						$myHelpId = $camilaWT->getWorktableRecordIdByKeyColumn($wtFrom, "CODICE FISCALE", $cf);
						if ($myHelpId != '') {
							$suggIds[] = $myHelpId;
							$ov = Array();
							$ov[$ovColName1] = $row[1];
							$ov[$ovColName2] = $row[2];
							$ov[$ovColName3] = $row[3];
							$ov[$ovColName4] = $k0;
							camila_information_text($cf . ' | ' . $row[1].' '.$row[2].' | Nucleo famigliare: '.$row[3]);
							$overrides[$myHelpId] = $ov;
						}
					}
				}
			}

			if (count($suggIds)>0) {
				if (!$camilaWT->insertSuggestionRecords($wtFrom, $wtTo2, implode($suggIds, ','),$overrides))
					camila_error_text("Si è verificato un errore nell'inserimento!");
				else
					$inserted = true;
			}
		} else {
			camila_error_text("Il codice QR Code inserito non è valido.");
		}

		$text = new CHAW_text('');
		$_CAMILA['page']->add_text($text);
		if ($inserted) {
			$myLink = new CHAW_link('Registra nuovo QR Code', 'index.php?dashboard=' . $_REQUEST['dashboard']);
			$myLink->set_css_class('btn btn-md btn-default btn-info');
			$myLink->set_br(2);
			$_CAMILA['page']->add_link($myLink);
		}
		else
			$form->draw();
	} else {
		camila_error_text('Inserire contenuto QR Code!');
		$form->draw();
	}
}
	else
		$form->draw();

?>