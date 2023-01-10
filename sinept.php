<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;
?>


<?php
global $wpdb;
$file_xml = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/woocommerce-synchronization-1c/files/site1/temp/analog.xml';

if (file_exists($file_xml)) {


    /* New Metod */
            $reader = new XMLReader();
            //var_dump($file_xml);
            $reader->open($file_xml); // указываем ридеру что будем парсить этот файл
            // циклическое чтение документа

            $analog = array();
            while( ($reader->read()) ) {
                if($reader->nodeType == XMLReader::ELEMENT) {
                    // если находим элемент <card>
                    if($reader->name == 'НомерТовара1') {
                        $reader->read();
                        $analog1 = (string)$reader->value;
                        //echo $analog1. "<br>";
                    }
                    if($reader->name == 'НомерТовара2') {
                        $reader->read();
                        $analog2 = (string)$reader->value;
                        //$analog[ strtolower((string)$analog1) ][] = strtolower((string)$analog2);
                    }
                }
                if($reader->nodeType == XMLReader::END_ELEMENT){
                    if ($reader->name == 'Аналог'){
                        $analog[ (string)$analog1 ][] = (string)$analog2;
                    }
                }
            }

           /* */

/* Old Metod 
    $xml_reader = new XMLReader();
    $xml_reader->open($file_xml);
    while($xml_reader->read() && $xml_reader->name != 'Аналог');
    
    $analog=array();

    while($xml_reader->name == 'Аналог') {
        $xml = new SimpleXMLElement($xml_reader->readOuterXML());
        $analog[strtolower((string)$xml->НомерТовара1)][]=(string)$xml->НомерТовара2;
        $xml_reader->next('Аналог');
        unset($xml);
    } 
    /* */

    //print_r($analog[strtolower($product->get_sku())]);
    if(isset($analog[ $product->get_sku() ])) {
        echo "<h2 style='text-align:center;'>Аналоги товара:</h2>";
        echo "
        <table  style='margin: 0; border-bottom: 2px solid black;border-left: 2px solid black;border-right: 2px solid black;'>
            <tr style='background-color: #f7b779;font-weight: bold;'>
                <td style='width: 400px; border-top: 2px solid #000000;padding-left: 10px;'>Производитель:</td>
                <td style='border-top: 2px solid #000000;width: 250px;text-align: center;'>OEM код</td>
                <td style='border-top: 2px solid #000000;width: 250px;text-align: center;'>Наличие</td>
                <td style='border-top: 2px solid #000000;text-align: center;border-right: 0px!important;'>Информация</td>
            </tr>";
        foreach ($analog[ $product->get_sku() ] as $key => $value) {
            $sql = "SELECT * FROM `wp_postmeta` WHERE `meta_value`  = '" . $value . "' AND  `meta_key`='_sku' LIMIT 1";
            $result = $wpdb->get_results($sql);//print_r($result);
            //echo '<br>';
            $numleft  = $product->get_stock_quantity(); 
            if (!empty($result[0]->post_id)) {
			    	$terms = get_the_terms( $result[0]->post_id, 'product_cat' );
				    if( $terms ){
					    $term = array_shift( $terms );
		    		}
                $valueS = get_post($result[0]->post_id);
                //$sqlZ = "SELECT * FROM `wp_wc_product_meta_lookup` WHERE `product_id` = '" . $result[0]->post_id . "'";
                //$resultZ = $wpdb->get_results($sqlZ);//print_r($result);
                //echo "<br><br>";
                $sqlZ = "SELECT * FROM `wp_postmeta` WHERE `post_id`  = '" . $result[0]->post_id . "' AND  `meta_key`='_stock' LIMIT 1";
                $resultZ = $wpdb->get_results($sqlZ);//print_r($result);*/
                //if(!empty($valueS->guid))

                //var_dump($resultZ[0]);
                if( ( (int)$resultZ[0]->meta_value ) > 0){
                    $have .= "
                    <tr style='align-items: center; background-color:#ffffed;font-weight: bold;'>
                        <td style='width: 400px; border-top: 2px solid #000000;vertical-align: middle;padding-left: 10px;'>" . $term->name . "</td>
                        <td style='border-top: 2px solid #000000;color:#292255;vertical-align: middle;width: 250px; text-align:left; padding-left: 10px;'>". $value ."</td>
                        <td style='border-top: 2px solid #000000;vertical-align: middle;width: 250px;text-align: center;'><p style='color:#29d826;font-size:16px;font-weight:bold; text-align:left; padding-left: 10px;'>". (int)$resultZ[0]->meta_value ." в наличии</p></td>
                        <td style='border-top: 2px solid #000000;vertical-align: middle;text-align: center;border-right: 0px!important;'><a class='btn_analog' href='" . $valueS->guid . "'>Перейти к товару</a></td>
                    </tr>";
                } else {
                    $not_have .= "
                    <tr style='align-items: center; background-color:#ffffed;font-weight: bold;'>
                        <td style='width: 400px; border-top: 2px solid #000000;vertical-align: middle;padding-left: 10px;'>" . $term->name . "</td>
                        <td style='border-top: 2px solid #000000;color:#292255;vertical-align: middle;width: 250px; text-align:left; padding-left: 10px;'>". $value ."</td>
                        <td style='border-top: 2px solid #000000;vertical-align: middle;width: 250px;text-align: center;'><p style='color:#292255;font-size:16px;font-weight:bold; text-align:left; padding-left: 10px;'>". "Нет в наличии</p></td>
                        <td style='border-top: 2px solid #000000;vertical-align: middle;text-align: center;border-right: 0px!important;'><a class='btn_analog' href='" . $valueS->guid . "'>Перейти к товару</a></td>
                    </tr>";
                }


            }
            //unset($valueS);
        }
        echo $have . $not_have; //viewer
    echo "</table>";
    } else {
        echo "<h2 style='border-top: 2px solid #000000;vertical-align: middle;text-align: center;'>Аналоги не найдены</h2>";
    }
    unset($analog);
} else {
    echo "Не удалось загрузить информацию об аналогах";
}

echo "<style>
.elementor-column-gap-default .elementor-row .elementor-column .elementor-element-populated{
border-radius: 0px !important;
padding:0px !important;
}
section.elementor-section .elementor-top-section .elementor-element .elementor-element-005133e .elementor-section-boxed .elementor-section-height-default .elementor-section-height-default{
padding-top: 15px;
}
.btn_analog{
    border: 1px solid #292255;
    padding: 5px;
    border-radius: 5px;
    background: #292255;
    color: white;
}
.btn_analog:visited{
    border: 1px solid #292255;
    padding: 5px;
    border-radius: 5px;
    background: #292255;
    color: white;
}
.btn_analog:hover{
    border: 1px solid #000;
    padding: 5px;
    border-radius: 5px;
    background: #fff;
    color: black;
}
.elementor-296471 .elementor-element.elementor-element-3c0a48a .elementor-element-populated{
    border-width: 0px;
}
td {
    border-right: 1px solid black;
}
</style>";
?>
