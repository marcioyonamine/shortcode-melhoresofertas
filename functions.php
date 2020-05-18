<?php

function lista_ofertas($atts){
	
	//coloca os parâmetros em variáveis
	extract(shortcode_atts(array("operadora" => NULL, "servico" => NULL, "tipo" => NULL, "segmento" => NULL, "valor_min" => NULL, "valor_max" => NULL), $atts));
	
	$produtos = converterObjParaArray(api_melhor_oferta_request());

	$produtosFiltrados = [];


	// filtro operadora
	if(isset($operadora)){
		for($i = 0; $i < count($produtos['lista']); $i++){
			if($produtos['lista'][$i]['categoria'] == $operadora){
				array_push($produtosFiltrados,$produtos['lista'][$i]); 
			
			}
		}	
	}else{
		$produtosFiltrados = $produtos['lista'];
	}

	//filtro tipo
	if(isset($tipo)){
		$temp = [];
		for($i = 0; $i < count($produtosFiltrados); $i++){
			if($produtosFiltrados[$i]['tipo'] == $tipo){
				array_push($temp,$produtosFiltrados[$i]); 
			
			}
		}	
		$produtosFiltrados = $temp;
	}
	

	//filtro valor min
	if(isset($valor_min)){
		$temp = [];
		for($i = 0; $i < count($produtosFiltrados); $i++){
			if($produtosFiltrados[$i]['valor_original'] >= $valor_min){
				array_push($temp,$produtosFiltrados[$i]); 
			
			}
		}	
		$produtosFiltrados = $temp;
	}

	//filtro valor max
	if(isset($valor_min)){
		$temp = [];
		for($i = 0; $i < count($produtosFiltrados); $i++){
			if($produtosFiltrados[$i]['valor_original'] <= $valor_max){
				array_push($temp,$produtosFiltrados[$i]); 
			}
		}	
		$produtosFiltrados = $temp;
	}
	

	//render
	$html= '<div class="owl-carousel area-planos" id="card-planos">';
	
	
	for($i = 0; $i < count($produtosFiltrados); $i++){
		$html .= '<div class="item-planos">';
		$html .= '';
		$html .= '<div class="top">';
        $html .= '            <div class="left">';
        $html .= '               <span class="categ">'.$produtosFiltrados[$i]['tipo'].'</span>';
        $html .= '                <span class="plano">'.$produtosFiltrados[$i]['produto'].'</span>';
        $html .= '            </div>';
        $html .= '            <span class="img-planos">';
        $html .= '      <img src="'.$produtosFiltrados[$i]['logo_produto'].'" alt="'.$produtosFiltrados[$i]['produto'].'" />';
        $html .= '            </span>';
        $html .= '        </div>';
        $html .= '        <div class="inclusos">';
		foreach($produtosFiltrados[$i]['vantagens'] as $vantagem_plano){
			$html .='			<div class="itens">';
            $icon_vantagem = $vantagem_plano['imagem'];
            $html .='           <img src="'.$icon_vantagem.'" alt="'.$vantagem_plano['texto'].'">';
			$html .='				<span>'.$vantagem_plano['texto'].'</span>';
			$html .='					</div>';
		}
        $html .='        </div>
                <table class="tb-card">
                    <tbody>
                        <tr>
                            <td>&nbsp;</td>
                            <td rowspan="2" class="real">';
							 $valor_final = str_replace('.',',', $produtosFiltrados[$i]['valor_original']);
        $html .=                           $valor_final;
		$html .='						</td>
                        </tr>
                        <tr>
                            <td class="cifrao">R$</td>
                        </tr>
                        <tr>';
		$html .='       <td colspan="2" class="obs">'.$produtosFiltrados[$i]['carencia'].'</td>
                        </tr>
                    </tbody>
                </table>';
		$html .='<a target="_blank" href="'.$produtosFiltrados[$i]['link_compra'].'" class="btn-assineAgora">';
		$html .= $produtosFiltrados[$i]['texto_botao']; 
        $html .='        </a>
            </div>';

		
	}


    $html .= "</div>";

	return $html;

}
add_shortcode('planos', 'lista_ofertas');

	

// retorna uma array com os produtos  
function api_melhor_oferta_request(){
	

	$server = $GLOBALS['server'];
	$authorization = $GLOBALS['authorization'];

	// para usar quando passar parametros para a api
	//$dados = http_build_query($att);
	//$getUrl = $server."?".$dados;

	$getUrl = $server; //utilizar quando não houver parâmetros

	$curl = curl_init($getUrl);
	curl_setopt($curl, CURLOPT_URL, $getUrl);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorization));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


	$r = json_decode(curl_exec($curl));

	curl_close($curl);

	return $r;
	
}

function converterObjParaArray($data) { //função que transforma objeto vindo do json em array
	if(is_object($data)) {
		$data = get_object_vars($data);
	}
	if(is_array($data)) {
		return array_map(__FUNCTION__, $data);
	}else{
		return $data;
	}
}



