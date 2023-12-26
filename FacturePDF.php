<?php
    require('fpdf186/fpdf.php');
    require_once 'modele/products.php';

    
    function pdf($product, $user){
        // le mettre au debut car plante si on declare $mysqli avant !
        $pdf = new FPDF( 'P', 'mm', 'A4' );

        if (isset($_SESSION['user']) && $_SESSION['user']!=null){
            $theUser=$_SESSION['user'];
        }else{
            $theUser=$_SESSION['userTemp'];
        }


        // on sup les 2 cm en bas
        $pdf->SetAutoPagebreak(False);
        $pdf->SetMargins(0,0,0);

        // nb de page pour le multi-page : 18 lignes
        $nb_page = ceil(count($_SESSION['lastOrder'][0]['products'])/18);

        $num_page = 1; $limit_inf = 0; $limit_sup = 18;
        While ($num_page <= $nb_page){
            $pdf->AddPage();
            
            // logo : 80 de largeur et 55 de hauteur
            $pdf->Image('productimages/livetoeat.jpeg', 10, 10, 80, 75);

            // n° page en haute à droite
            $pdf->SetXY( 120, 5 ); $pdf->SetFont( "Arial", "B", 12 ); $pdf->Cell( 160, 8, $num_page . '/' . $nb_page, 0, 0, 'C');
            
            //today date
            $date=date("d/m/Y");
            $order_id=$_SESSION['lastOrder'][0]['order_id'];

            $annee = date('Y');
            $num_fact = utf8_decode("FACTURE N°") . $annee .'-'.$order_id;
            $pdf->SetLineWidth(0.1); $pdf->SetFillColor(192); $pdf->Rect(120, 15, 85, 8, "DF");
            $pdf->SetXY( 120, 15 ); $pdf->SetFont( "Arial", "B", 12 ); $pdf->Cell( 85, 8, $num_fact, 0, 0, 'C');
            
            //nom du fichier final
            $nom_file = "fact_" . $annee ."-".$order_id.".pdf";
            $i=0;
        
            // date facture
            $pdf->SetFont('Arial','',11); $pdf->SetXY( 122, 30 );
            $pdf->Cell( 60, 8, "LYON , le " . $date, 0, 0, '');
            
            // si derniere page alors afficher total
            if ($num_page == $nb_page)
            {
                // les totaux, on n'affiche que le HT. le cadre après les lignes, demarre a 213
                $pdf->SetLineWidth(0.1); $pdf->SetFillColor(192); $pdf->Rect(115, 213, 90, 8, "DF");
                // HT, la TVA et TTC sont calculés après
                $nombre_format_francais = utf8_decode("Total : " . number_format($_SESSION['lastOrder'][0]['total'], 2, ',', ' ') ." euros");
                $pdf->SetFont('Arial','',10); $pdf->SetXY( 130, 213 ); $pdf->Cell( 63, 8, $nombre_format_francais, 0, 0, 'C');
                // reglement
                $pdf->SetXY( 5, 225 ); $pdf->Cell( 38, 5, utf8_decode("Mode de Règlement :"), 0, 0, 'R'); $pdf->Cell( 55, 5, $_SESSION['lastOrder'][0]['payment_type'], 0, 0, 'L');
            }
            
            // observations
            $pdf->SetFont( "Arial", "BU", 10 ); $pdf->SetXY( 5, 75 ) ; $pdf->Cell($pdf->GetStringWidth("Observations"), 0, "Observations", 0, "L");

            // adr fact du client
            $userData=$user->get_user_by_id($theUser); 
            $pdf->SetFont('Arial','B',11); $x = 110 ; $y = 50;
            $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, utf8_decode($userData[0]->forname.' '.$userData[0]->surname), 0, 0, ''); $y += 4;
            if ($userData[0]->add1) { $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, utf8_decode($userData[0]->add1), 0, 0, ''); $y += 4;}
            if ($userData[0]->add2) { $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, utf8_decode($userData[0]->add2), 0, 0, ''); $y += 4;}
            $pdf->SetXY( $x, $y ); $pdf->Cell( 100, 8, utf8_decode($userData[0]->postcode.' '.$userData[0]->add3), 0, 0, ''); $y += 4;
        
            // ***********************
            // le cadre des articles
            // ***********************
            // cadre avec 18 lignes max ! et 118 de hauteur --> 95 + 118 = 213 pour les traits verticaux
            $pdf->SetLineWidth(0.1); $pdf->Rect(5, 95, 200, 118, "D");
            // cadre titre des colonnes
            $pdf->Line(5, 105, 205, 105);
            // les traits verticaux colonnes
            $pdf->Line(145, 95, 145, 213); $pdf->Line(158, 95, 158, 213); /*$pdf->Line(176, 95, 176, 213);*/ $pdf->Line(180, 95, 180, 213);
            // titre colonne
            $pdf->SetXY( 1, 96 ); $pdf->SetFont('Arial','B',8); $pdf->Cell( 140, 8, utf8_decode("Libellé"), 0, 0, 'C');
            $pdf->SetXY( 145, 96 ); $pdf->SetFont('Arial','B',8); $pdf->Cell( 13, 8, utf8_decode("Qté"), 0, 0, 'C');
            $pdf->SetXY( 156, 96 ); $pdf->SetFont('Arial','B',8); $pdf->Cell( 22, 8, "PRIX UNIT", 0, 0, 'C');
            $pdf->SetXY( 182, 96 ); $pdf->SetFont('Arial','B',8); $pdf->Cell( 22, 8, "TOTAL", 0, 0, 'C');
            
            // les articles
            $pdf->SetFont('Arial','',8);
            $y = 97;
            // 1ere page = LIMIT 0,18 ;  2eme page = LIMIT 18,36 etc...
            foreach ($_SESSION['lastOrder'][0]['products'] as $key => $value){
                if ($i>=$limit_inf && $i<$limit_sup) { 
                    $theProduct=$product->get_product_by_id($key);
                    // libelle
                    $pdf->SetXY( 7, $y+9 ); $pdf->Cell( 140, 5, utf8_decode($theProduct[0]->name), 0, 0, 'L');
                    // qte
                    $pdf->SetXY( 145, $y+9 ); $pdf->Cell( 13, 5, strrev(wordwrap(strrev($value), 3, ' ', true)), 0, 0, 'R');
                    // PU
                    $nombre_format_francais = number_format($theProduct[0]->price, 2, ',', ' ');
                    $pdf->SetXY( 158, $y+9 ); $pdf->Cell( 18, 5, $nombre_format_francais, 0, 0, 'R');
                    // total
                    $nombre_format_francais = number_format($theProduct[0]->price*$value, 2, ',', ' ');
                    $pdf->SetXY( 187, $y+9 ); $pdf->Cell( 18, 5, $nombre_format_francais, 0, 0, 'R');
                    
                    $pdf->Line(5, $y+14, 205, $y+14);
                    
                    $y += 6;
                }
                $i++;
            }
            $i=0;

            // **************************
            // pied de page
            // **************************
            $pdf->SetLineWidth(0.1); $pdf->Rect(5, 260, 200, 6, "D");
            $pdf->SetXY( 1, 260 ); $pdf->SetFont('Arial','',7);
            $pdf->Cell( $pdf->GetPageWidth(), 7, utf8_decode("Clause de réserve de propriété (loi 80.335 du 12 mai 1980) : Les marchandises vendues demeurent notre propriété jusqu'au paiement intégral de celles-ci."), 0, 0, 'C');
            
            $y1 = 270;
            //Positionnement en bas et tout centrer
            $pdf->SetXY( 1, $y1 ); $pdf->SetFont('Arial','B',10);
            $pdf->Cell( $pdf->GetPageWidth(), 5, "REF BANCAIRE : FR76 xxx - BIC : xxxx", 0, 0, 'C');
            
            $pdf->SetFont('Arial','',10);
            
            $pdf->SetXY( 1, $y1 + 4 ); 
            $pdf->Cell( $pdf->GetPageWidth(), 5, utf8_decode("Les délices de KAM & CC"), 0, 0, 'C');
            
            $pdf->SetXY( 1, $y1 + 8 );
            $pdf->Cell( $pdf->GetPageWidth(), 5, utf8_decode("12 Avenue des Délices 69000 LYON"), 0, 0, 'C');

            $pdf->SetXY( 1, $y1 + 12 );
            $pdf->Cell( $pdf->GetPageWidth(), 5, utf8_decode("01 02 03 04 05 lesdélices@gmail.com"), 0, 0, 'C');

            $pdf->SetXY( 1, $y1 + 16 );
            $pdf->Cell( $pdf->GetPageWidth(), 5, "KAM&CC.com", 0, 0, 'C');
        
            // par page de 18 lignes
            $num_page++; $limit_inf += 18; $limit_sup += 18;
        }
        $pdf->Output("I", "nom_file");
}  
?>