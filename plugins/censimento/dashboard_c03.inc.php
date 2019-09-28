<?php
$camilaUI->insertTitle('Area incontro - Ricerca nucleo familiare', 'search');

$camilaWT = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];

$wtFrom = 'OSPITI ATTESI';

$lang = 'it';
$camilaTemplate = new CamilaTemplate($lang);

$form = new phpform($_REQUEST['dashboard'],'index.php?dashboard=' . $_REQUEST['dashboard']);
$form->submitbutton = 'Cerca';
$form->drawrules = true;
$form->preservecontext = true;

new form_textbox($form, 'fiscalcode', 'Codice Fiscale', false, 50, 200);
new form_textbox($form, 'lastname', 'Cognome', false, 50, 200);
new form_textbox($form, 'firstname', 'Nome', false, 50, 200);
$form->fields['fiscalcode']->set_css_class('form-control');
$form->fields['lastname']->set_css_class('form-control');
$form->fields['firstname']->set_css_class('form-control');
$form->fields['fiscalcode']->autofocus = true;

if ($form->process())
{
	$fiscalcode = $form->fields['fiscalcode']->value;
	$lastname = $form->fields['lastname']->value;
	$firstname = $form->fields['firstname']->value;

	if ($fiscalcode != '' || $lastname != '' || $firstname != '') {

		$where = " WHERE ";
		$hasMore = false;

		if ($lastname != '') {
			$where .= ($hasMore ? ' AND ' : '') ."\${OSPITI ATTESI.COGNOME} LIKE ".$camilaWT->db->qstr($lastname.'%');
			$hasMore = true;
		}

		if ($firstname != '') {
			$where .= ($hasMore ? ' AND ' : '') ."\${OSPITI ATTESI.NOME} LIKE ".$camilaWT->db->qstr('%'.$firstname.'%');
			$hasMore = true;
		}

		if ($fiscalcode != '') {
			$where .= ($hasMore ? ' AND ' : '') ."(\${OSPITI ATTESI.IDENTIFICATIVO NUCLEO FAMILIARE} = ".$camilaWT->db->qstr($fiscalcode) . " OR \${OSPITI ATTESI.CODICE FISCALE} = ".$camilaWT->db->qstr($fiscalcode) . ")";
			$hasMore = true;
		}

		$sql = "Select Id,\${OSPITI ATTESI.COGNOME},\${OSPITI ATTESI.NOME},\${OSPITI ATTESI.INDIRIZZO ABITAZIONE},\${OSPITI ATTESI.CITTA' ABITAZIONE},\${OSPITI ATTESI.IDENTIFICATIVO NUCLEO FAMILIARE} FROM \${".$wtFrom."} " . $where . " ORDER BY \${OSPITI ATTESI.IDENTIFICATIVO NUCLEO FAMILIARE}";

		$result = $camilaWT->startExecuteQuery($sql);
		$count = 0;
		while (!$result->EOF) {
			$a = $result->fields;
			$camilaUI->insertText($a[1].' '.$a[2] . ' | ' . $a[3].' | '.$a[4], 0);
			$camilaUI->insertLink('?dashboard=c01&cf='.urlencode($a[5]), $a[5]);
			$count++;
			$result->MoveNext();
		}
		if ($count == 0) {
			$camilaUI->insertWarning('La ricerca non ha restituito alcun risultato!');
			$form->draw();
		}
	} else {
		camila_error_text('Inserire almeno un campo per la ricerca!');
		$form->draw();
	}
}
	else
		$form->draw();

?>