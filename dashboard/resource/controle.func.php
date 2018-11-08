<?php

    DEFINE ('POR_PAGAMENTO', 1);
    DEFINE ('POR_LIMITE', 2);

    DEFINE ('FLAGS', 4);

    function congela($idCli, $motivo = POR_PAGAMENTO){
		global $conCad;
		
	    $qUpdatePlanoVencido = "UPDATE plano SET PLAN_status = 4 WHERE PLAN_id_cli = $idCli";
        $resultUpdatePlanoVencido = mysqli_query($conCad, $qUpdatePlanoVencido);
        
        $qSelectMotivoCongelamento = "SELECT CONT_motivo_congelamento FROM controle WHERE CONT_id_cli = '$idCli'";
        $resultSelectMotivo = mysqli_query($conCad, $qSelectMotivoCongelamento);

        $motivoCongelamento = mysqli_fetch_array($resultSelectMotivo)['CONT_motivo_congelamento'];

        //maneja o motivo do congelamento. Ele é um inteiro que representa flags no formato _ _ _
        // _ _ 1 representa motivo = pagamento vencido
        // _ 1 _ representa motivo = limite atingido
        // 0 _ _ não representa nada ainda, mas tá implementado pra poder ter mais um motivo de congelamento

        $motivoCongelamento = decbin($motivoCongelamento); // int pra string binário.  2 -> "10" (por exemplo)
        
        $motivoCongelamento = str_pad($motivoCongelamento, FLAGS, "0", STR_PAD_LEFT); //cap de caracteres na string pra 3, preenchidos com '0'. "1" -> "001" (por exemplo)

        
        $motivoCongelamento[abs($motivo - FLAGS)] = "1"; // seta a flag do motivo pra 1"
        
        $motivoCongelamento = bindec($motivoCongelamento);

	    
	    //e atualiza o controle interno sobre o motivo do congelamento
	    $qUpdateMotivo = "UPDATE controle set CONT_motivo_congelamento = $motivoCongelamento WHERE CONT_id_cli = '$idCli'";
	    $resultUpdateMotivo = mysqli_query($conCad, $qUpdateMotivo);
	}

    function descongela($idCli, $motivo = POR_PAGAMENTO){
		global $conCad;
        
        $qSelectMotivoCongelamento = "SELECT CONT_motivo_congelamento FROM controle WHERE CONT_id_cli = '$idCli'";
        $resultSelectMotivo = mysqli_query($conCad, $qSelectMotivoCongelamento);

        $motivoCongelamento = mysqli_fetch_array($resultSelectMotivo)['CONT_motivo_congelamento'];

        //maneja o motivo do congelamento. Ele é um inteiro que representa flags no formato _ _ _
        // _ _ 1 representa motivo = pagamento vencido
        // _ 1 _ representa motivo = limite atingido
        // 0 _ _ não representa nada ainda, mas tá implementado pra poder ter mais um motivo de congelamento

        $motivoCongelamento = decbin($motivoCongelamento); // int pra string binário.  2 -> "10" (por exemplo)
        
        $motivoCongelamento = str_pad($motivoCongelamento, FLAGS, "0", STR_PAD_LEFT); //cap de caracteres na string pra 3, preenchidos com '0'. "1" -> "001" (por exemplo)

        
        $motivoCongelamento[abs($motivo - FLAGS)] = "0"; // seta a flag do motivo pra "0"
        
        $motivoCongelamento = bindec($motivoCongelamento);

	    
	    //e atualiza o controle interno sobre o motivo do congelamento
	    $qUpdateMotivo = "UPDATE controle set CONT_motivo_congelamento = $motivoCongelamento WHERE CONT_id_cli = '$idCli'";
        $resultUpdateMotivo = mysqli_query($conCad, $qUpdateMotivo);
        
        if($motivoCongelamento == 0){
            $qUpdatePlanoVencido = "UPDATE plano SET PLAN_status = 1 WHERE PLAN_id_cli = $idCli";
            $resultUpdatePlanoVencido = mysqli_query($conCad, $qUpdatePlanoVencido);
        }
	}
?>