<?php

//ATTIVO TUTTI GLI ERRORI

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//CONNSESSIONE AL SERVER MYSQL

require('datiDB.php');

//FILE XML 

$xml = simplexml_load_file('articoli.xml') or die ('Errore: non è stato trovato alcun file');

//DICHIARO VARIABILI 

$i = 0; 
$x = 0;

//CICLO FOREACH PER INDAGARE XML 

foreach($xml->Products as $Prd){

    foreach($xml->Products[$i]->Product as $Prodotto){

        $titolo = $Prodotto->Description;
            $InteralID = $Prodotto->InternalID;
            $Code = $Prodotto->Code;
            $Description = $Prodotto->Description;
            $DescriptionHtml = $Prodotto->DescriptionHtml;
            $Category = $Prodotto->Category;
            //SOTTOCATEGORIA ... $prototto->Subcategory;
            $Vat = $Prodotto->Vat;
            $Um = $Prodotto->Um;
            $NetPrice1 = $Prodotto->NetPrice1;
            $NetPrice2 = $Prodotto->NetPrice2;
            $NetPrice3 = $Prodotto->NetPrice3;
            $NetPrice4 = $Prodotto->NetPrice4;
            $NetPrice5 = $Prodotto->NetPrice5;
            $NetPrice6 = $Prodotto->NetPrice6;
            $NetPrice7 = $Prodotto->NetPrice7;
            $NetPrice8 = $Prodotto->NetPrice8;
            $NetPrice9 = $Prodotto->NetPrice9;
            $GrossPrice1 = $Prodotto->GrossPrice1;
            $GrossPrice2 = $Prodotto->GrossPrice2;
            $GrossPrice3 = $Prodotto->GrossPrice3;
            $GrossPrice4 = $Prodotto->GrossPrice4;
            $GrossPrice5 = $Prodotto->GrossPrice5;
            $GrossPrice6 = $Prodotto->GrossPrice6;
            $GrossPrice7 = $Prodotto->GrossPrice7;
            $GrossPrice8 = $Prodotto->GrossPrice8;
            $GrossPrice9 = $Prodotto->GrossPrice9;
            $SupplierCode = $Prodotto->SupplierCode;
            $SupplierName = $Prodotto->SupplierName;
            $SupplierProductCode = $Prodotto->SupplierProductCode;
            $SupplierNetPrice = $Prodotto->SupplierNetPrice;
            $SupplierGrossPrice = $Prodotto->SupplierGrossPrice;
            $SizeUm = $Prodotto->SizeUm;
            $WeightUm = $Prodotto->WeightUm;
            $ManageWarehouse = $Prodotto->ManageWarehouse;
            $MinStock = $Prodotto->MinStock;
            $AvailableQty = $Prodotto->AvailableQty;
            $Notes = $Prodotto->Notes;

//CREO LO SLUG 

        $Stringa_Slug = str_replace(" ","-", $titolo);
        $Stringa_Slug = $Stringa_Slug.$Code;

//LIBRERIA CATEGORIE 

        $Lista_Categorie = ['girante turbina' => 223, 'girante compressore' => 224];
        $CategoriaInEntrata = $Category;
        $CategoriaInEntrataMinuscola = trim(strtolower($CategoriaInEntrata));

        if(in_array($CategoriaInEntrataMinuscola, array_keys($Lista_Categorie))){

            $ID_categoria = $Lista_Categorie[$CategoriaInEntrataMinuscola];

        }else{
            $ID_categoria = 222;
        }

        $x++;
        $titolo_piu_SKU = $titolo." ".$Code;
        $Prod_esistente = false;

        $sqlWoo = "SELECT * FROM 'wp_posts' p left join wp_postmeta pm on p.ID = pm.post_id WHERE 'post_type' = 'product' and meta_key = '_stock' and post_name = '".$Stringa_Slug."'";
        $result2 = mysqli_query($link,$sqlWoo);

        while($row2 = mysqli_fetch_assoc($result2)){

            $sqlUpdateQty = "Update wp_postmeta set meta_value = ".$AvailableQty." where post_id = ( select id from wp_posts where post name = '".$Stringa_Slug."' and ping_status = 'open') and meta_key = '_stock'";
            $sqlUpdateQty."\n";
            mysqli_query($link,$sqlUpdateQty);

            $sqlUpdateQty2 = "Update wp_postmeta set meta_value = ".$NetPrice1." where post_id = ( select id from wp_posts where post name = '".$Stringa_Slug."' and ping_status = 'open') and meta_key = '_price'";
            $sqlUpdateQty2."\n";
            mysqli_query($link,$sqlUpdateQty2);

            $sqlUpdateQty3 = "Update wp_posts set post_content = '".$Notes."' where post_name = '".$Stringa_Slug."'";
            $sqlUpdateQty3."\n";
            mysqli_query($link,$sqlUpdateQty3);

            $Prod_esistente = true;

        }

        //SE NON ESISTE DEVO INSERIRE

        if(!$Prod_esistente){

            $sqlMaxId = "Select max(id) as maxid from 'wp_posts'";
            $resultMaxId = mysqli_query($link,$sqlMaxId);
            $MaxIdArr = mysqli_fetch_assoc($resultMaxId);
            $MaxId = $MaxIdArr["maxid"];
            $MaxId++;
            $sqlMaxMetaId = "Select max(meta_id) maxmetaid from wp_postmeta";
            $resultMetaId = mysqli_query($link,$sqlMaxMetaId);
            $MaxMetaIdArr = mysqli_fetch_assoc($resultMetaId);
            $MaxMetaId = $MaxMetaIdArr["maxmetaid"];
            $MaxMetaId++;

            $sqlInsertWpPostMeta1 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId, $MaxId, '_stock', ".$AvailableQty.");";
            $sqlInsertWpPostMeta2 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +1, $MaxId, '_sku', ".$Code.");";
            $sqlInsertWpPostMeta3 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +2, $MaxId, '_downloadable', 'no');";
            $sqlInsertWpPostMeta4 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +3, $MaxId, '_virtual', 'no');";
            $sqlInsertWpPostMeta5 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +4, $MaxId, '_price', ".$NetPrice1.");";
            $sqlInsertWpPostMeta6 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +5, $MaxId, '_visibility', 'visible');";
            $sqlInsertWpPostMeta7 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +6, $MaxId, '_stock_status', 'instock');";
            $sqlInsertWpPostMeta8 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +7, $MaxId, '_backorders', 'yes');";
            $sqlInsertWpPostMeta9 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +8, $MaxId, '_managestock', 'yes');";
            $sqlInsertWpPostMeta10 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +9, $MaxId, '_regularprice', ".$NetPrice1.");";
            $sqlInsertWpPostMeta11 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +10,  $MaxId, '_tax_status, 'none');";
            $sqlInsertWpPostMeta12 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +11, $MaxId, '_featured', 'yes');";
            $sqlInsertWpPostMeta13 = "Insert INTO wp_postmeta(meta_id, post_id, meta_key, meta_value) values ($MaxMetaId +12, $MaxId, '_thumbnail_id', '2824');";

            mysqli_query($link, $sqlInsertWpPostMeta1);
            mysqli_query($link, $sqlInsertWpPostMeta2);
            mysqli_query($link, $sqlInsertWpPostMeta3);
            mysqli_query($link, $sqlInsertWpPostMeta4);
            mysqli_query($link, $sqlInsertWpPostMeta5);
            mysqli_query($link, $sqlInsertWpPostMeta6);
            mysqli_query($link, $sqlInsertWpPostMeta7);
            mysqli_query($link, $sqlInsertWpPostMeta8);
            mysqli_query($link, $sqlInsertWpPostMeta9);
            mysqli_query($link, $sqlInsertWpPostMeta10);
            mysqli_query($link, $sqlInsertWpPostMeta11);
            mysqli_query($link, $sqlInsertWpPostMeta12);
            mysqli_query($link, $sqlInsertWpPostMeta13);

            $sqlInsertwpPosts = "Insert into wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title,post_excerpt, post_status, comment_status, ping_status, post_name, post_modified, post_modified_gmt, post_parent, guid, menu_order, post_type ) 
            Values ($MaxId , 1, '".date('Y-m-d h:i:s')."', '".date('Y-m-d h:i:s')."', '".$Notes."', '".$titolo_piu_SKU."', '', 'publish', 'open', 'open', '".$Stringa_Slug."', '".date('Y-m-d h:i:s')."', '".date('Y-m-d h:i:s')."', 0, 'http://www.italiaturbo.it/prodotto/".$Stringa_Slug."/', 0, 'product'); ";
            echo $sqlInsertwpPosts."<br>";
        mysqli_query($link,$sqlInsertwpPosts); 

        $ultimoIdInserito = mysqli_insert_id($link);
        echo $ultimoIdInserito;

        //ASSEGNO CATEGORIA 

        $sqlwpTermsRelationships2 = "Insert into wp_term_relationships (object_id, term_taxonomy_id, term_order) values ($MaxId, $ID_categoria, 0);";
        mysqli_query($link,$sqlwpTermsRelationships2);

        }
     
    }

    echo "</br></br>";
    $i++;
    
}


?>
