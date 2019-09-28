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
$ovCol5 = "AREA D'ATTESA";
$ovColName5 = "";

$keyColumn = "CODICE FISCALE";

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
	if (strtolower($ovCol5) == strtolower($b['name'])) {
		$ovColName5 = $b['col_name'];
	}
	$result2->MoveNext();
}

$lang = 'it';

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
						$myHelpId = $camilaWT->getWorktableRecordIdByKeyColumn($wtFrom, $keyColumn, $cf);
						if ($myHelpId != '') {
							$suggIds[] = $myHelpId;
							$ov = Array();
							$ov[$ovColName1] = $row[1];
							$ov[$ovColName2] = $row[2];
							$ov[$ovColName3] = $row[3];
							$ov[$ovColName4] = $k0;
							$ov[$ovColName5] = $k1;
							camila_information_text($cf . ' | ' . $row[1].' '.$row[2].' | Nucleo famigliare: '.$row[3]);
							$overrides[$myHelpId] = $ov;
						} else {
							$fields8=Array();
							$values8=Array();
							$fields8[] = $keyColumn;
							$fields8[] = $ovCol1;
							$fields8[] = $ovCol2;
							$fields8[] = $ovCol3;
							$fields8[] = $ovCol4;
							$fields8[] = $ovCol5;
							$values8[] = $cf;
							$values8[] = $row[1];
							$values8[] = $row[2];
							$values8[] = $row[3];
							$values8[] = $k0;
							$values8[] = $k1;
							//Controllare esito
							
							$res = $camilaWT->insertRow($wtTo2, $lang, $fields8, $values8);
							if ($res === false) {
								camila_error_text('Errore inserimento: ' . $cf . ' | ' . $row[1].' '.$row[2].' | Nucleo famigliare: '.$row[3]);
							} else {
								camila_information_text($cf . ' | ' . $row[1].' '.$row[2].' | Nucleo famigliare: '.$row[3]);
								$inserted = true;
							}
						}
					}
				}
			}

			if (count($suggIds)>0) {
				if (!$camilaWT->insertSuggestionRecords($wtFrom, $wtTo2, implode($suggIds, ','),$overrides))
					camila_error_text("Si è verificato un errore nell'inserimento!");
				else $inserted = true;
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